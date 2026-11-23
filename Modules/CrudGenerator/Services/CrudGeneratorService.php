<?php

namespace Modules\CrudGenerator\Services;

use Modules\CrudGenerator\Models\CrudDefinition;
use Modules\Menu\Models\Menu;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * CRUD Generator: membuat migrasi, model, controller, dan menu secara otomatis.
 *
 * Mendukung dua target database:
 * - tenant  -> database mitra (database/migrations/tenant, model App\Models\Tenant).
 * - central -> database pusat  (database/migrations, model App\Models).
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
        $title   = $definition['title'];
        $model   = Str::studly($definition['model']);
        $key     = Str::slug($definition['key'] ?: Str::snake(Str::pluralStudly($model)), '_');
        $table   = Str::snake(Str::pluralStudly($model));
        $fields  = $definition['fields'];
        $icon    = $definition['icon'] ?? 'table';
        $hasCabang = (bool) ($definition['has_cabang'] ?? false);

        $connection = ($definition['connection'] ?? 'tenant') === 'central' ? 'central' : 'tenant';
        $isTenant   = $connection === 'tenant';
        $namespace  = $isTenant ? 'App\\Models\\Tenant' : 'App\\Models';
        $timestamp  = date('Y_m_d_His');

        // 1. Migrasi (ke folder sesuai target database)
        $migrationPath = $isTenant
            ? database_path("migrations/tenant/{$timestamp}_create_{$table}_table.php")
            : database_path("migrations/{$timestamp}_create_{$table}_table.php");
        File::put($migrationPath, $this->migrationStub($table, $fields, $hasCabang, $isTenant));

        // 2. Model
        $modelPath = $isTenant
            ? app_path("Models/Tenant/{$model}.php")
            : app_path("Models/{$model}.php");
        File::put($modelPath, $this->modelStub($model, $table, $fields, $hasCabang, $connection, $namespace));

        // 3. Controller
        $controllerPath = app_path("Http/Controllers/Admin/{$model}Controller.php");
        File::put($controllerPath, $this->controllerStub($model, $key, $title, $fields, $hasCabang, $namespace));

        // 4. Menu
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

        // 5. Simpan definisi + assign ke role
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
            'migration'  => $migrationPath,
            'model'      => $modelPath,
            'controller' => $controllerPath,
            'menu'       => $menu,
        ];
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
        $cabangRelation = $hasCabang ? <<<PHP


    public function cabang(): BelongsTo
    {
        return \$this->belongsTo(Cabang::class, 'cabang_id');
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
        $fieldsPhp = $this->exportFields($fields);
        $hasCabangStr = $hasCabang ? 'true' : 'false';
        $fqcn = "\\{$namespace}\\{$model}";

        return <<<PHP
<?php

namespace App\Http\Controllers\Admin;

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
