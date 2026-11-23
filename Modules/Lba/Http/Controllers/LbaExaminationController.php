<?php

namespace Modules\Lba\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Lba\Models\LbaExamination;

/**
 * CRUD Pemeriksaan LBA (LbaExamination) pada database mitra.
 *
 * View memakai view inti bersama (admin.crud).
 * Route didaftarkan oleh LbaServiceProvider (Routes/web.php).
 */
class LbaExaminationController extends BaseCrudController
{
    protected string $model = LbaExamination::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.lba_examinations';
    protected string $menuKey = 'lba_examinations';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Pemeriksaan LBA';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'patient_id', 'type' => 'relation', 'label' => 'Pasien', 'nullable' => false,
                'relation' => ['model' => \Modules\Pelanggan\Models\Pelanggan::class, 'label' => 'name']],
            ['name' => 'branch_id', 'type' => 'relation', 'label' => 'Cabang', 'nullable' => false,
                'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'appointment_id', 'type' => 'relation', 'label' => 'Janji Temu', 'nullable' => true,
                'relation' => ['model' => \Modules\Appointments\Models\Appointment::class, 'label' => 'service_type']],
            ['name' => 'mode', 'type' => 'select', 'label' => 'Mode', 'nullable' => false,
                'options' => ['manual' => 'Manual', 'assisted' => 'Assisted']],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false,
                'options' => [
                    'draft'    => 'Draft',
                    'captured' => 'Captured',
                    'analyzed' => 'Analyzed',
                    'reviewed' => 'Reviewed',
                    'final'    => 'Final',
                ]],
            ['name' => 'captured_at', 'type' => 'datetime', 'label' => 'Waktu Pemotretan', 'nullable' => true],
            ['name' => 'instrument', 'type' => 'string', 'label' => 'Instrumen', 'nullable' => true],
            ['name' => 'magnification', 'type' => 'string', 'label' => 'Magnifikasi', 'nullable' => true],
            ['name' => 'notes', 'type' => 'text', 'label' => 'Catatan', 'nullable' => true],
        ];
    }
}
