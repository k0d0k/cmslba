<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant\Appointment;
use App\Models\Tenant\AppointmentStatusHistory;
use Illuminate\Http\Request;

/**
 * Contoh modul CRUD Janji Temu (Appointments) pada database mitra.
 *
 * Menu `appointments` diberi `resource = 'Appointment'` sehingga route CRUD
 * `admin/appointments` otomatis terdaftar (lihat AppServiceProvider) — inilah
 * yang menghidupkan link menu yang tadinya mati.
 */
class AppointmentController extends BaseCrudController
{
    protected string $model = Appointment::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.appointments';
    protected string $menuKey = 'appointments';
    protected bool $hasCabang = false;

    /** Daftar status janji temu. */
    public const STATUSES = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];

    protected function title(): string
    {
        return __('common.appointment');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'patient_id', 'type' => 'relation', 'label' => __('common.patient'), 'nullable' => false,
                'relation' => ['model' => \App\Models\Tenant\Pelanggan::class, 'label' => 'name']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => false,
                'relation' => ['model' => \App\Models\Tenant\Cabang::class, 'label' => 'name']],
            ['name' => 'staff_user_id', 'type' => 'relation', 'label' => __('common.staff'), 'nullable' => true,
                'relation' => ['model' => \App\Models\User::class, 'label' => 'name_with_role', 'with' => ['roles']]],
            ['name' => 'starts_at', 'type' => 'datetime', 'label' => __('appointments.starts_at'), 'nullable' => false],
            ['name' => 'ends_at', 'type' => 'datetime', 'label' => __('appointments.ends_at'), 'nullable' => false],
            ['name' => 'service_type', 'type' => 'string', 'label' => __('appointments.service_type'), 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => __('common.status'), 'nullable' => false,
                'options' => $this->statusOptions(), 'default' => 'scheduled'],
            ['name' => 'notes', 'type' => 'text', 'label' => __('common.note'), 'nullable' => true],
        ];
    }

    protected function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => __('appointments.status_' . $status)])
            ->all();
    }

    /**
     * Setiap perubahan status dicatat ke tabel appointment_status_histories.
     */
    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();
        $oldStatus = $record->status;

        $request->validate($this->rules());
        $record->update($this->values($request));

        if ($record->status !== $oldStatus) {
            AppointmentStatusHistory::create([
                'appointment_id' => $record->id,
                'from_status'    => $oldStatus,
                'to_status'      => $record->status,
                'changed_by'     => (string) $this->user()?->id,
                'changed_at'     => now(),
            ]);
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }
}
