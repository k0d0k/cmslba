<?php

namespace Modules\Consultation\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Illuminate\Http\Request;
use Modules\Consultation\Models\Consultation;
use Modules\Consultation\Models\ConsultationStatusHistory;

/**
 * CRUD Konsultasi (Consultation) pada database mitra.
 *
 * View memakai view inti bersama admin.crud.
 * Route didaftarkan oleh ConsultationServiceProvider (Routes/web.php).
 */
class ConsultationController extends BaseCrudController
{
    protected string $model = Consultation::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.consultations';
    protected string $menuKey = 'consultations';
    protected bool $hasCabang = false;

    /** Daftar kanal konsultasi. */
    public const CHANNELS = ['sina_chat', 'whatsapp', 'web', 'phone'];

    /** Daftar tipe konsultasi. */
    public const TYPES = ['consultation', 'follow_up'];

    /** Daftar status konsultasi. */
    public const STATUSES = ['scheduled', 'in_progress', 'completed', 'cancelled'];

    protected function title(): string
    {
        return 'Konsultasi';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'patient_id', 'type' => 'relation', 'label' => __('common.patient'), 'nullable' => false,
                'relation' => ['model' => \Modules\Pelanggan\Models\Pelanggan::class, 'label' => 'name']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => false,
                'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'staff_user_id', 'type' => 'relation', 'label' => __('common.staff'), 'nullable' => true,
                'relation' => ['model' => \Modules\User\Models\User::class, 'label' => 'name_with_role', 'with' => ['roles']]],
            ['name' => 'appointment_id', 'type' => 'relation', 'label' => 'Janji Temu', 'nullable' => true,
                'relation' => ['model' => \Modules\Appointments\Models\Appointment::class, 'label' => 'id']],
            ['name' => 'channel', 'type' => 'select', 'label' => 'Kanal', 'nullable' => false,
                'options' => $this->channelOptions(), 'default' => 'sina_chat'],
            ['name' => 'type', 'type' => 'select', 'label' => __('common.type'), 'nullable' => false,
                'options' => $this->typeOptions(), 'default' => 'consultation'],
            ['name' => 'status', 'type' => 'select', 'label' => __('common.status'), 'nullable' => false,
                'options' => $this->statusOptions(), 'default' => 'scheduled'],
            ['name' => 'scheduled_at', 'type' => 'datetime', 'label' => 'Waktu Terjadwal', 'nullable' => false],
            ['name' => 'started_at', 'type' => 'datetime', 'label' => 'Waktu Mulai', 'nullable' => false],
            ['name' => 'ended_at', 'type' => 'datetime', 'label' => 'Waktu Selesai', 'nullable' => true],
            ['name' => 'reason', 'type' => 'text', 'label' => 'Alasan', 'nullable' => true],
            ['name' => 'summary', 'type' => 'text', 'label' => 'Ringkasan', 'nullable' => true],
        ];
    }

    protected function channelOptions(): array
    {
        return collect(self::CHANNELS)
            ->mapWithKeys(fn ($channel) => [$channel => $channel])
            ->all();
    }

    protected function typeOptions(): array
    {
        return collect(self::TYPES)
            ->mapWithKeys(fn ($type) => [$type => $type])
            ->all();
    }

    protected function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => $status])
            ->all();
    }

    /**
     * Setiap perubahan status dicatat ke tabel consultation_status_histories.
     */
    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();
        $oldStatus = $record->status;

        $request->validate($this->rules());
        $record->update($this->values($request));

        if ($record->status !== $oldStatus) {
            ConsultationStatusHistory::create([
                'consultation_id' => $record->id,
                'from_status'     => $oldStatus,
                'to_status'       => $record->status,
                'changed_by'      => (string) $this->user()?->id,
                'changed_at'      => now(),
            ]);
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }
}
