<?php

namespace Modules\Lba\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Lba\Models\LbaReportVersion;

/**
 * CRUD Laporan LBA (LbaReportVersion) pada database mitra.
 *
 * View memakai view inti bersama (admin.crud).
 * Route didaftarkan oleh LbaServiceProvider (Routes/web.php).
 */
class LbaReportVersionController extends BaseCrudController
{
    protected string $model = LbaReportVersion::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.lba_report_versions';
    protected string $menuKey = 'lba_report_versions';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Laporan LBA';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'assessment_id', 'type' => 'relation', 'label' => 'Asesmen', 'nullable' => false,
                'relation' => ['model' => \Modules\Lba\Models\LbaAssessment::class, 'label' => 'id']],
            ['name' => 'version', 'type' => 'integer', 'label' => 'Versi', 'nullable' => false],
            ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false,
                'options' => [
                    'generated' => 'Generated',
                    'approved'  => 'Approved',
                    'released'  => 'Released',
                    'revoked'   => 'Revoked',
                ]],
            ['name' => 'locale', 'type' => 'string', 'label' => 'Locale', 'nullable' => false],
            ['name' => 'checksum', 'type' => 'string', 'label' => 'Checksum', 'nullable' => false],
            ['name' => 'disk', 'type' => 'string', 'label' => 'Disk', 'nullable' => false],
            ['name' => 'object_key', 'type' => 'string', 'label' => 'Object Key', 'nullable' => false],
            ['name' => 'mime_type', 'type' => 'string', 'label' => 'MIME Type', 'nullable' => false],
            ['name' => 'size_bytes', 'type' => 'integer', 'label' => 'Ukuran (byte)', 'nullable' => false],
            ['name' => 'generated_at', 'type' => 'datetime', 'label' => 'Waktu Dibuat', 'nullable' => false],
        ];
    }
}
