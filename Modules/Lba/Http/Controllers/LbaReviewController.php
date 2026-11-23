<?php

namespace Modules\Lba\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Lba\Models\LbaReview;

/**
 * CRUD Review LBA (LbaReview) pada database mitra.
 *
 * View memakai view inti bersama (admin.crud).
 * Route didaftarkan oleh LbaServiceProvider (Routes/web.php).
 */
class LbaReviewController extends BaseCrudController
{
    protected string $model = LbaReview::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.lba_reviews';
    protected string $menuKey = 'lba_reviews';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Review LBA';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'assessment_id', 'type' => 'relation', 'label' => 'Asesmen', 'nullable' => false,
                'relation' => ['model' => \Modules\Lba\Models\LbaAssessment::class, 'label' => 'id']],
            ['name' => 'reviewer_id', 'type' => 'string', 'label' => 'Reviewer', 'nullable' => false],
            ['name' => 'decision', 'type' => 'select', 'label' => 'Keputusan', 'nullable' => false,
                'options' => [
                    'approved' => 'Approved',
                    'returned' => 'Returned',
                    'rejected' => 'Rejected',
                ]],
            ['name' => 'notes', 'type' => 'text', 'label' => 'Catatan', 'nullable' => true],
            ['name' => 'decided_at', 'type' => 'datetime', 'label' => 'Waktu Keputusan', 'nullable' => false],
        ];
    }
}
