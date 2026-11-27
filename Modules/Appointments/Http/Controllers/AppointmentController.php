<?php

namespace Modules\Appointments\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Illuminate\Http\Request;
use Modules\Appointments\Models\Appointment;
use Modules\Appointments\Models\AppointmentServiceType;
use Modules\Appointments\Models\AppointmentStatus;
use Modules\Appointments\Models\AppointmentStatusHistory;
use Modules\Mitra\Models\Mitra;

/**
 * CRUD Janji Temu (Appointments) pada database mitra.
 *
 * View modul: appointments::crud (Resources/views/crud).
 * Route didaftarkan oleh AppointmentsServiceProvider (Routes/web.php).
 */
class AppointmentController extends BaseCrudController
{
    protected string $model = Appointment::class;
    protected string $view = 'appointments::crud';
    protected string $routeName = 'admin.appointments';
    protected string $menuKey = 'appointments';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return __('common.appointment');
    }

    protected function fields(): array
    {
        return [
            // mitra_uuid disembunyikan pada form (auto mitra aktif) & pada tabel index.
            ['name' => 'mitra_uuid', 'type' => 'relation', 'label' => __('common.mitra'), 'nullable' => true,
                'hide_in_form' => true, 'hide_in_table' => true,
                'relation' => ['name' => 'mitra', 'model' => Mitra::class, 'label' => 'name']],
            ['name' => 'patient_id', 'type' => 'relation', 'label' => __('common.patient'), 'nullable' => false,
                'relation' => ['model' => \Modules\Pelanggan\Models\Pelanggan::class, 'label' => 'name']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => false,
                'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'staff_user_id', 'type' => 'relation', 'label' => __('common.staff'), 'nullable' => true,
                'relation' => ['model' => \Modules\User\Models\User::class, 'label' => 'name_with_role', 'with' => ['roles']]],
            ['name' => 'starts_at', 'type' => 'datetime', 'label' => __('appointments.starts_at'), 'nullable' => false],
            ['name' => 'ends_at', 'type' => 'datetime', 'label' => __('appointments.ends_at'), 'nullable' => false],
            ['name' => 'service_type_id', 'type' => 'relation', 'label' => __('appointments.service_type'), 'nullable' => false,
                'relation' => ['name' => 'serviceType', 'model' => AppointmentServiceType::class, 'label' => 'name']],
            ['name' => 'status_id', 'type' => 'relation', 'label' => __('common.status'), 'nullable' => false,
                'relation' => ['name' => 'status', 'model' => AppointmentStatus::class, 'label' => 'name']],
            ['name' => 'notes', 'type' => 'text', 'label' => __('common.note'), 'nullable' => true],
        ];
    }

    protected function values(Request $request): array
    {
        $data = parent::values($request);

        // mitra_uuid selalu mengikuti mitra yang sedang aktif (tidak dari input form).
        $data['mitra_uuid'] = $this->currentMitra()?->uuid;

        return $data;
    }

    /** Mitra aktif (binding `active_mitra`) atau mitra milik user yang login. */
    protected function currentMitra(): ?Mitra
    {
        if (app()->bound('active_mitra')) {
            return app('active_mitra');
        }

        $user = $this->user();

        return $user?->mitra_id ? Mitra::find($user->mitra_id) : null;
    }

    /**
     * Setiap perubahan status dicatat ke tabel appointment_status_histories.
     */
    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();
        $oldStatusId = $record->status_id;

        $request->validate($this->rules());
        $record->update($this->values($request));

        if ((int) $record->status_id !== (int) $oldStatusId) {
            AppointmentStatusHistory::create([
                'appointment_id'  => $record->id,
                'from_status_id'  => $oldStatusId,
                'to_status_id'    => $record->status_id,
                'changed_by'      => (string) $this->user()?->id,
                'changed_at'      => now(),
            ]);
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }
}
