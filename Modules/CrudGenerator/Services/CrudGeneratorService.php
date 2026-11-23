<?php

namespace Modules\CrudGenerator\Services;

use Modules\CrudGenerator\Models\CrudDefinition;
use Modules\Menu\Models\Menu;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * CRUD Generator (Modular Native: PSR-4 + ServiceProvider).
 *
 * Setiap CRUD yang digenerate menghasilkan SATU modul mandiri di Modules/<Model>/
 * yang mendaftarkan route-nya sendiri melalui ServiceProvider-nya.
 *
 * Struktur hasil generate:
 *   Modules/<Model>/Http/Controllers/<Model>Controller.php
 *   Modules/<Model>/Models/<Model>.php
 *   Modules/<Model>/Providers/<Model>ServiceProvider.php   (otomatis didaftarkan di bootstrap/providers.php)
 *   Modules/<Model>/Routes/web.php
 *   Modules/<Model>/Database/Migrations/<...>.php          (bila connection = central)
 *   Modules/<Model>/Database/Migrations/Tenant/<...>.php   (bila connection = tenant)
 *
 * Mendukung dua target database:
 * - tenant  -> database mitra (Modules/<Model>/Database/Migrations/Tenant) dijalankan oleh App\Services\TenantService.
 * - central -> database pusat  (Modules/<Model>/Database/Migrations) dijalankan lewat ServiceProvider (loadMigrationsFrom).
 */
class CrudGeneratorService
{
    protected array $columnMap = [
        'string'   => 'string',
        'text'     => 'text',
        'email'    => 'string',
        'integer'  => 'integer',
        'decimal'  => 'decimal',
        'boolean'  => 'boolean',
        'date'     => 'date',
        'datetime' => 'dateTime',
        'select'   => 'string',
        'relation' => 'unsignedBigInteger',
    ];

    /**
     * @param array $definition ['title','key','model','fields'(array),'roles'(array),'icon','has_cabang','connection']
     */
    public function generate(array $definition): array
    {
        $title     = $definition['title'];
        $model     = Str::studly($definition['model']);
        $key       = Str::slug($definition['key'] ?: Str::snake(Str::pluralStudly($model)), '_');
        $table     = Str::snake(Str::pluralStudly($model));
        $fields    = $definition['fields'];
        $icon      = $definition['icon'] ?? 'table';
        $hasCabang = (bool) ($definition['has_cabang'] ?? false);

        $connection = ($definition['connection'] ?? 'tenant') === 'central' ? 'central' : 'tenant';
        $isTenant   = $connection === 'tenant';
        $namespace  = "Modules\\{$model}\\Models";
        $timestamp  = date('Y_m_d_His');

        // Satu CRUD = satu modul. Nama folder & namespace memakai nama model.
        $modulesRoot = rtrim((string) config('lbacloud.modules_path', base_path('Modules')), '/\\');
        $modulePath  = $modulesRoot . '/' . $model;

        if (File::isDirectory($modulePath)) {
            throw new \RuntimeException(
                "Modul \"{$model}\" sudah ada di Modules/. Gunakan nama model yang lain."
            );
        }

        // Kerangka direktori modul (Modular Native).
        File::ensureDirectoryExists($modulePath . '/Http/Controllers');
        File::ensureDirectoryExists($modulePath . '/Models');
        File::ensureDirectoryExists($modulePath . '/Providers');
        File::ensureDirectoryExists($modulePath . '/Routes');

        // 1. Migrasi (folder sesuai target database).
        if ($isTenant) {
            File::ensureDirectoryExists($modulePath . '/Database/Migrations/Tenant');
            $migrationPath = "{$modulePath}/Database/Migrations/Tenant/{$timestamp}_create_{$table}_table.php";
        } else {
            File::ensureDirectoryExists($modulePath . '/Database/Migrations');
            $migrationPath = "{$modulePath}/Database/Migrations/{$timestamp}_create_{$table}_table.php";
        }
        File::put($migrationPath, $this->migrationStub($table, $fields, $hasCabang, $isTenant));

        // 2. Model
        $modelPath = "{$modulePath}/Models/{$model}.php";
        File::put($modelPath, $this->modelStub($model, $table, $fields, $hasCabang, $connection, $namespace));

        // 3. Controller
        $controllerPath = "{$modulePath}/Http/Controllers/{$model}Controller.php";
        File::put($controllerPath, $this->controllerStub($model, $key, $title, $fields, $hasCabang, $namespace));

        // 4. Routes (didaftarkan sendiri oleh modul, bukan generator menu inti).
        $routesPath = "{$modulePath}/Routes/web.php";
        File::put($routesPath, $this->routeStub($model, $key));

        // 5. ServiceProvider + registrasi di bootstrap/providers.php.
        $providerPath = "{$modulePath}/Providers/{$model}ServiceProvider.php";
        File::put($providerPath, $this->providerStub($model, $isTenant));
        $this->registerProvider($model);

        // 6. Menu
        $menu = Menu::create([
            'parent_id'  => $definition['parent_id'] ?? null,
            'key'        => $key,
            'title'      => ['id' => $title, 'en' => $title, 'ms' => $title],
            'icon'       => $icon,
            'type'       => 'link',
            'route_name' => "admin.{$key}.index",
            'sort'       => (int) Menu::max('sort') + 1,
            'is_crud'    => true,
            'resource'   => $model,
        ]);

        // 7. Simpan definisi + assign ke role
        CrudDefinition::create([
            'model_name' => $model,
            'table_name' => $table,
            'connection' => $connection,
            'menu_id'    => $menu->id,
            'icon'       => $icon,
            'fields'     => $fields,
            'roles'      => $definition['roles'] ?? [],
            'created_by' => auth()->id(),
        ]);

        $this->assignRoles($menu, $definition['roles'] ?? []);

        return [
            'module'     => $modulePath,
            'migration'  => $migrationPath,
            'model'      => $modelPath,
            'controller' => $controllerPath,
            'routes'     => $routesPath,
            'provider'   => $providerPath,
            'menu'       => $menu,
        ];
    }

    /**
     * Daftarkan ServiceProvider modul baru ke bootstrap/providers.php
     * (idempoten: tidak dobel bila sudah terdaftar).
     */
    protected function registerProvider(string $model): void
    {
        $file = base_path('bootstrap/providers.php');
        if (! File::exists($file)) {
            return;
        }

        $fqcn    = "Modules\\{$model}\\Providers\\{$model}ServiceProvider::class";
        $content = File::get($file);

        if (str_contains($content, $fqcn)) {
            return;
        }

        $pos = strrpos($content, '];');
        if ($pos === false) {
            return;
        }

        $content = substr($content, 0, $pos) . "    {$fqcn},\n" . substr($content, $pos);
        File::put($file, $content);
    }

    protected function assignRoles(Menu $menu, array $roles): void
    {
        $allRoles = array_unique(array_merge(['admin_principal', 'user_principal'], $roles));

        foreach ($allRoles as $roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if ($role) {
                $menu->roles()->syncWithoutDetaching([$role->id => [
                    'can_view'   => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'can_export' => true,
                ]]);
            }
        }
    }

    protected function migrationStub(string $table, array $fields, bool $hasCabang, bool $isTenant): string
    {
        $columns = '';
        if ($isTenant) {
            // Setiap tabel pada database mitra menyimpan uuid mitra pemiliknya.
            $columns .= "            \$table->string('mitra_uuid')->nullable()->index();\n";
        }
        if ($hasCabang) {
            $columns .= "            \$table->unsignedBigInteger('cabang_id')->nullable()->index();\n";
        }
        foreach ($fields as $f) {
            if (in_array($f['name'], ['slug', 'cabang_id', 'mitra_uuid'], true)) {
                continue;
            }
            $columns .= '            ' . $this->columnLine($f) . "\n";
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
{$columns}            \$table->string('slug')->unique();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    protected function columnLine(array $f): string
    {
        $name = $f['name'];
        $type = $f['type'] ?? 'string';
        $nullable = ($f['nullable'] ?? false) ? '->nullable()' : '';
        $unique = (! empty($f['unique']) && $type !== 'boolean') ? '->unique()' : '';

        $line = match ($type) {
            'text'     => "\$table->text('{$name}'){$nullable}",
            'integer'  => "\$table->integer('{$name}'){$nullable}",
            'decimal'  => "\$table->decimal('{$name}', 15, 2){$nullable}",
            'boolean'  => "\$table->boolean('{$name}')->default(false)",
            'date'     => "\$table->date('{$name}'){$nullable}",
            'datetime' => "\$table->dateTime('{$name}'){$nullable}",
            'relation' => "\$table->unsignedBigInteger('{$name}'){$nullable}",
            'email'    => "\$table->string('{$name}'){$nullable}{$unique}",
            default    => "\$table->string('{$name}'){$nullable}{$unique}",
        };

        return $line . ';';
    }

    protected function modelStub(string $model, string $table, array $fields, bool $hasCabang, string $connection, string $namespace): string
    {
        $isTenant = $connection === 'tenant';

        $fillableParts = ["'slug'"];
        if ($isTenant) {
            $fillableParts[] = "'mitra_uuid'";
        }
        if ($hasCabang) {
            $fillableParts[] = "'cabang_id'";
        }
        foreach ($fields as $f) {
            $fillableParts[] = "'{$f['name']}'";
        }
        $fillable = implode(', ', $fillableParts);

        $casts = [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'boolean') {
                $casts[] = "'{$f['name']}' => 'boolean'";
            } elseif (in_array($f['type'] ?? '', ['date', 'datetime'], true)) {
                $casts[] = "'{$f['name']}' => 'date'";
            } elseif (($f['type'] ?? '') === 'decimal') {
                $casts[] = "'{$f['name']}' => 'decimal:2'";
            }
        }
        $castsStr = $casts ? implode(",\n        ", $casts) . ',' : '';

        $slugSource = collect($fields)->firstWhere('name', 'name') ? 'name' : (collect($fields)->first()['name'] ?? 'id');
        $slugSource = $slugSource === 'id' ? 'id' : $slugSource;

        // Import & trait tambahan untuk database mitra.
        $mitraImport = $isTenant ? "\nuse App\\Models\\Concerns\\HasMitraUuid;" : '';
        $mitraTrait  = $isTenant ? "\n    use HasMitraUuid;" : '';
        $belongsToImport = $hasCabang ? "\nuse Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;" : '';
        // Relasi ke modul Cabang memakai FQCN karena berada di modul berbeda.
        $cabangRelation = $hasCabang ? <<<PHP


    public function cabang(): BelongsTo
    {
        return \$this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'cabang_id');
    }
PHP : '';

        return <<<PHP
<?php

namespace {$namespace};

use App\Models\Concerns\HasSlug;{$mitraImport}
use Illuminate\Database\Eloquent\Model;{$belongsToImport}

class {$model} extends Model
{
    use HasSlug;{$mitraTrait}

    protected \$connection = '{$connection}';

    protected \$table = '{$table}';

    protected \$fillable = [{$fillable}];

    protected \$casts = [
        {$castsStr}
    ];

    public \$slug_source = '{$slugSource}';{$cabangRelation}
}
PHP;
    }

    protected function controllerStub(string $model, string $key, string $title, array $fields, bool $hasCabang, string $namespace): string
    {
        $fieldsPhp    = $this->exportFields($fields);
        $hasCabangStr = $hasCabang ? 'true' : 'false';
        $fqcn         = "\\{$namespace}\\{$model}";
        $controllerNs = "Modules\\{$model}\\Http\\Controllers";

        return <<<PHP
<?php

namespace {$controllerNs};

use App\Http\Controllers\Admin\BaseCrudController;

class {$model}Controller extends BaseCrudController
{
    protected string \$model = {$fqcn}::class;
    protected string \$view = 'admin.crud';
    protected string \$routeName = 'admin.{$key}';
    protected string \$title = '{$title}';
    protected string \$menuKey = '{$key}';
    protected bool \$hasCabang = {$hasCabangStr};
    protected array \$fields = {$fieldsPhp};
}
PHP;
    }

    protected function routeStub(string $model, string $key): string
    {
        $controllerFqcn = "Modules\\{$model}\\Http\\Controllers\\{$model}Controller";

        return <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use {$controllerFqcn};

/*
|--------------------------------------------------------------------------
| Routes modul {$model} (hasil CRUD Generator)
|--------------------------------------------------------------------------
| Didaftarkan sendiri oleh modul melalui {$model}ServiceProvider sehingga
| bersifat mandiri. Nama route mengikuti konvensi inti: admin.{$key}.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        \$controller = {$model}Controller::class;

        Route::get('{$key}', [\$controller, 'index'])->name('{$key}.index');
        Route::get('{$key}/data', [\$controller, 'data'])->name('{$key}.data');
        Route::get('{$key}/create', [\$controller, 'create'])->name('{$key}.create');
        Route::post('{$key}', [\$controller, 'store'])->name('{$key}.store');
        Route::get('{$key}/{slug}/edit', [\$controller, 'edit'])->name('{$key}.edit');
        Route::put('{$key}/{slug}', [\$controller, 'update'])->name('{$key}.update');
        Route::delete('{$key}/{slug}', [\$controller, 'destroy'])->name('{$key}.destroy');
    });
PHP;
    }

    protected function providerStub(string $model, bool $isTenant): string
    {
        $namespace = "Modules\\{$model}\\Providers";

        // Modul tenant tidak memuat migrasi lewat provider (dijalankan TenantService
        // dari Modules/<Model>/Database/Migrations/Tenant). Modul central memuatnya.
        $migrations = $isTenant
            ? ''
            : "\n        \$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');";

        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul {$model} (Modular Native).
 *
 * Modul hasil CRUD Generator ini mendaftarkan route-nya sendiri
 * (Routes/web.php) dan memakai view bersama 'admin.crud' sehingga tidak
 * memiliki view sendiri.
 */
class {$model}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \$this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');{$migrations}
    }
}
PHP;
    }

    protected function exportFields(array $fields): string
    {
        $lines = [];
        foreach ($fields as $f) {
            $parts = [
                'name' => "'{$f['name']}'",
                'type' => "'" . ($f['type'] ?? 'string') . "'",
                'label' => "'" . ($f['label'] ?? Str::headline($f['name'])) . "'",
                'nullable' => ($f['nullable'] ?? false) ? 'true' : 'false',
            ];
            if (! empty($f['options'])) {
                $opts = collect($f['options'])->map(fn ($v, $k) => "'{$k}' => '{$v}'")->implode(', ');
                $parts['options'] = "[{$opts}]";
            }
            if (! empty($f['relation'])) {
                $parts['relation'] = "['model' => '{$f['relation']['model']}', 'label' => '{$f['relation']['label']}']";
            }
            $inner = collect($parts)->map(fn ($v, $k) => "'{$k}' => {$v}")->implode(', ');
            $lines[] = "        [{$inner}],";
        }

        return "[\n" . implode("\n", $lines) . "\n    ]";
    }
}
