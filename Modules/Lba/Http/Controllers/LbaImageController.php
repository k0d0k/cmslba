<?php

namespace Modules\Lba\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Lba\Models\LbaImage;

/**
 * CRUD Gambar LBA (LbaImage) pada database mitra.
 *
 * View memakai view inti bersama (admin.crud).
 * Route didaftarkan oleh LbaServiceProvider (Routes/web.php).
 */
class LbaImageController extends BaseCrudController
{
    protected string $model = LbaImage::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.lba_images';
    protected string $menuKey = 'lba_images';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return 'Gambar LBA';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'examination_id', 'type' => 'relation', 'label' => 'Pemeriksaan', 'nullable' => false,
                'relation' => ['model' => \Modules\Lba\Models\LbaExamination::class, 'label' => 'id']],
            ['name' => 'disk', 'type' => 'string', 'label' => 'Disk', 'nullable' => false],
            ['name' => 'object_key', 'type' => 'string', 'label' => 'Object Key', 'nullable' => false],
            ['name' => 'original_name', 'type' => 'string', 'label' => 'Nama Asli', 'nullable' => true],
            ['name' => 'checksum', 'type' => 'string', 'label' => 'Checksum', 'nullable' => false],
            ['name' => 'mime_type', 'type' => 'string', 'label' => 'MIME Type', 'nullable' => false],
            ['name' => 'size_bytes', 'type' => 'integer', 'label' => 'Ukuran (byte)', 'nullable' => false],
            ['name' => 'source', 'type' => 'select', 'label' => 'Sumber', 'nullable' => false,
                'options' => [
                    'manual_import' => 'Manual Import',
                    'camera'        => 'Camera',
                    'upload'        => 'Upload',
                ]],
            ['name' => 'instrument_label', 'type' => 'string', 'label' => 'Label Instrumen', 'nullable' => true],
            ['name' => 'captured_at', 'type' => 'datetime', 'label' => 'Waktu Pemotretan', 'nullable' => true],
        ];
    }
}
