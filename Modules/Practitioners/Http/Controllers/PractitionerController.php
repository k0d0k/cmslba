<?php

namespace Modules\Practitioners\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Illuminate\Http\Request;
use Modules\Practitioners\Models\Practitioner;
use Modules\Practitioners\Models\PractitionerAuditLog;

/**
 * CRUD Praktisi (Practitioners) pada database mitra.
 *
 * View memakai view inti bersama admin.crud.
 * Route didaftarkan oleh PractitionersServiceProvider (Routes/web.php).
 */
class PractitionerController extends BaseCrudController
{
    protected string $model = Practitioner::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.practitioners';
    protected string $menuKey = 'practitioners';
    protected bool $hasCabang = false;

    /** Daftar status praktisi. */
    public const STATUSES = ['pending', 'active', 'suspended', 'expired', 'archived'];

    protected function title(): string
    {
        return __('practitioners.title');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'user_id', 'type' => 'relation', 'label' => __('common.user'), 'nullable' => false,
                'relation' => ['model' => \Modules\User\Models\User::class, 'label' => 'name']],
            ['name' => 'display_name', 'type' => 'string', 'label' => __('practitioners.display_name'), 'nullable' => false],
            ['name' => 'title', 'type' => 'string', 'label' => __('practitioners.title_label'), 'nullable' => true],
            ['name' => 'professional_body', 'type' => 'string', 'label' => __('practitioners.professional_body'), 'nullable' => true],
            ['name' => 'specialities', 'type' => 'text', 'label' => __('practitioners.specialities'), 'nullable' => true,
                'hint' => __('practitioners.specialities_hint')],
            ['name' => 'status', 'type' => 'select', 'label' => __('common.status'), 'nullable' => false,
                'options' => $this->statusOptions(), 'default' => 'pending'],
            ['name' => 'expires_at', 'type' => 'datetime', 'label' => __('practitioners.expires_at'), 'nullable' => true],
            ['name' => 'notes', 'type' => 'text', 'label' => __('common.note'), 'nullable' => true],
        ];
    }

    protected function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => __('practitioners.status_' . $status)])
            ->all();
    }

    /** Ubah input spesialisasi (dipisah koma) menjadi array JSON. */
    protected function values(Request $request): array
    {
        $data = parent::values($request);
        $data['specialities'] = $this->normalizeSpecialities($request->input('specialities'));

        return $data;
    }

    protected function normalizeSpecialities($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (blank($value)) {
            return [];
        }

        return collect(explode(',', (string) $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $request->validate($this->rules());

        $record = ($this->model)::create($this->values($request));
        $this->logAudit($record, 'created', $record->getAttributes());

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.created'));
    }

    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        $request->validate($this->rules($record->id));
        $before = $record->getOriginal();
        $record->update($this->values($request));

        $this->logAudit($record, 'updated', [
            'before' => $before,
            'after'  => $record->getAttributes(),
        ]);

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }

    public function destroy($slug)
    {
        $this->authorizeAbility('delete');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        $this->logAudit($record, 'deleted', $record->getAttributes());
        $record->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'message' => __('messages.deleted')]);
        }

        return back()->with('success', __('messages.deleted'));
    }

    /** Catat perubahan ke tabel practitioner_audit_logs. */
    protected function logAudit(Practitioner $practitioner, string $action, array $changes): void
    {
        PractitionerAuditLog::create([
            'practitioner_id' => $practitioner->id,
            'action'          => $action,
            'actor_id'        => (string) $this->user()?->id,
            'changes'         => $changes,
            'occurred_at'     => now(),
        ]);
    }
}
