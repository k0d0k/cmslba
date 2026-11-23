<?php

namespace Modules\Lba\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Lba\Models\LbaAssessment;

/**
 * CRUD Asesmen LBA (LbaAssessment) pada database mitra.
 *
 * View memakai view inti bersama (admin.crud).
 * Route didaftarkan oleh LbaServiceProvider (Routes/web.php).
 */
class LbaAssessmentController extends BaseCrudController
{
    protected string $model = LbaAssessment::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.lba_assessments';
    protected string $menuKey = 'lba_assessments';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Asesmen LBA';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'examination_id', 'type' => 'relation', 'label' => 'Pemeriksaan', 'nullable' => false,
                'relation' => ['model' => \Modules\Lba\Models\LbaExamination::class, 'label' => 'id']],
            ['name' => 'catalog_version', 'type' => 'string', 'label' => 'Versi Katalog', 'nullable' => false],
            ['name' => 'mode', 'type' => 'select', 'label' => 'Mode', 'nullable' => false,
                'options' => ['manual' => 'Manual', 'ai_assisted' => 'AI Assisted']],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false,
                'options' => [
                    'draft'     => 'Draft',
                    'submitted' => 'Submitted',
                    'reviewed'  => 'Reviewed',
                    'locked'    => 'Locked',
                ]],
            ['name' => 'program_phase', 'type' => 'select', 'label' => 'Fase Program', 'nullable' => false,
                'options' => [
                    'not_specified' => 'Not Specified',
                    'baseline'      => 'Baseline',
                    'follow_up'     => 'Follow Up',
                    'final'         => 'Final',
                ]],
            ['name' => 'notes', 'type' => 'text', 'label' => 'Catatan', 'nullable' => true],
        ];
    }
}
