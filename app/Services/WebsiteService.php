<?php

namespace App\Services;

/**
 * Struktur & nilai bawaan konten website/frontpage.
 * Konten bersifat multi-bahasa (id, en, ms) + media bersama.
 */
class WebsiteService
{
    /** Bahasa yang tersedia untuk konten website. */
    public static function locales(): array
    {
        return [
            'id' => ['label' => 'Indonesia', 'flag' => 'ID'],
            'en' => ['label' => 'English',   'flag' => 'EN'],
            'ms' => ['label' => 'Malaysia',  'flag' => 'MS-MY'],
        ];
    }

    /** Pilihan template tampilan frontpage (warna soft, elegant, kekinian). */
    public static function templates(): array
    {
        return [
            'default'  => 'Teal Klinis (bawaan)',
            'sage'     => 'Sage Soft',
            'sand'     => 'Sand Elegant',
            'lavender' => 'Lavender Modern',
            'sky'      => 'Sky Calm',
            'rose'     => 'Rose Elegant',
        ];
    }

    /** Kerangka kosong untuk satu bahasa. */
    public static function blankLocale(): array
    {
        return [
            'hero' => [
                'eyebrow'     => '',
                'title'       => '',
                'subtitle'    => '',
                'button_text' => '',
                'button_url'  => '',
                'image'       => '',
            ],
            'services' => [],
            'features' => [],
            'faq'      => [],
            'contact'  => ['email' => '', 'phone' => '', 'address' => ''],
            'seo'      => ['title' => '', 'description' => '', 'og_image' => ''],
        ];
    }

    /**
     * Konten bawaan (contoh) untuk website platform. Dipakai saat website
     * platform belum dibuat / belum diisi, agar frontpage tetap tampil rapi.
     */
    public static function defaultContent(): array
    {
        $mk = function (string $locale) {
            $t = [
                'id' => [
                    'eyebrow'  => 'Integrated Clinical Intelligence',
                    'title'    => 'Platform klinis terintegrasi untuk operasional laboratorium',
                    'subtitle' => 'Pendaftaran pasien, pemeriksaan mikroskopis digital, peninjauan oleh tenaga ahli, dan laporan privat yang terkontrol dalam satu sistem.',
                    'services' => [
                        ['title' => 'Principal', 'description' => 'Tata kelola platform: mitra, brand, langganan, modul, dan hak akses fitur dari satu tempat.', 'tag' => 'Tersedia'],
                        ['title' => 'Klinik',    'description' => 'Operasional harian: pasien, jadwal, pemeriksaan LBA, peninjauan hasil, dan dompet token AI.', 'tag' => 'Tersedia'],
                        ['title' => 'Pasien',    'description' => 'Portal ramah seluler untuk melihat jadwal dan hasil pemeriksaan.', 'tag' => 'Sebagian tersedia'],
                    ],
                    'features' => [
                        ['title' => 'Gambar mikroskopis privat', 'description' => 'Gambar asli disimpan privat dan tidak pernah diubah atau dipublikasikan.'],
                        ['title' => 'Peninjauan oleh manusia',   'description' => 'Saran AI tidak pernah final; keputusan klinis tetap di tangan tenaga ahli.'],
                        ['title' => 'Basis data mitra terisolasi', 'description' => 'Setiap mitra bekerja dalam batas datanya sendiri.'],
                        ['title' => 'Multi-mitra & multi-brand', 'description' => 'Satu deployment melayani banyak organisasi klinik.'],
                    ],
                    'faq' => [
                        ['question' => 'Apakah saran AI bersifat diagnosis?', 'answer' => 'Tidak. Saran AI hanya ditampilkan untuk dipertimbangkan dan wajib diverifikasi oleh operator atau peninjau berkualifikasi.'],
                        ['question' => 'Bagaimana data mitra disimpan?', 'answer' => 'Setiap mitra memiliki database terpisah sehingga data saling terisolasi.'],
                    ],
                    'seo' => [
                        'title'       => 'LBA Cloud — Integrated Clinical Intelligence',
                        'description' => 'Platform klinis terintegrasi untuk operasional laboratorium LBA.',
                    ],
                ],
                'en' => [
                    'eyebrow'  => 'Integrated Clinical Intelligence',
                    'title'    => 'An integrated clinical platform for laboratory operations',
                    'subtitle' => 'Patient registration, digital microscopy review by experts, and controlled private reports — all in one system.',
                    'services' => [
                        ['title' => 'Principal', 'description' => 'Platform governance: partners, brands, subscriptions, modules, and feature access in one place.', 'tag' => 'Available'],
                        ['title' => 'Clinic',    'description' => 'Daily operations: patients, scheduling, LBA examinations, result review, and AI token wallet.', 'tag' => 'Available'],
                        ['title' => 'Patient',   'description' => 'A mobile-friendly portal to view schedules and examination results.', 'tag' => 'Partially available'],
                    ],
                    'features' => [
                        ['title' => 'Private microscopy images', 'description' => 'Original images are stored privately and never altered or published.'],
                        ['title' => 'Human review',              'description' => 'AI suggestions are never final; clinical decisions remain with experts.'],
                        ['title' => 'Isolated partner databases', 'description' => 'Each partner works within its own data boundary.'],
                        ['title' => 'Multi-partner & multi-brand', 'description' => 'A single deployment serves many clinical organisations.'],
                    ],
                    'faq' => [
                        ['question' => 'Are AI suggestions a diagnosis?', 'answer' => 'No. AI suggestions are shown for consideration only and must be verified by a qualified operator or reviewer.'],
                        ['question' => 'How is partner data stored?', 'answer' => 'Each partner has a separate database, so data stays isolated.'],
                    ],
                    'seo' => [
                        'title'       => 'LBA Cloud — Integrated Clinical Intelligence',
                        'description' => 'An integrated clinical platform for LBA laboratory operations.',
                    ],
                ],
                'ms' => [
                    'eyebrow'  => 'Integrated Clinical Intelligence',
                    'title'    => 'Platform klinikal bersepadu untuk operasi makmal',
                    'subtitle' => 'Pendaftaran pesakit, semakan mikroskopi digital oleh pakar, dan laporan peribadi yang terkawal dalam satu sistem.',
                    'services' => [
                        ['title' => 'Principal', 'description' => 'Tadbir urus platform: rakan kongsi, brand, langganan, modul, dan akses ciri di satu tempat.', 'tag' => 'Tersedia'],
                        ['title' => 'Klinik',    'description' => 'Operasi harian: pesakit, jadual, pemeriksaan LBA, semakan keputusan, dan dompet token AI.', 'tag' => 'Tersedia'],
                        ['title' => 'Pesakit',   'description' => 'Portal mesra mudah alih untuk melihat jadual dan keputusan pemeriksaan.', 'tag' => 'Sebahagian tersedia'],
                    ],
                    'features' => [
                        ['title' => 'Imej mikroskop peribadi', 'description' => 'Imej asal disimpan secara peribadi dan tidak pernah diubah atau diterbitkan.'],
                        ['title' => 'Semakan oleh manusia',    'description' => 'Cadangan AI tidak pernah muktamad; keputusan klinikal kekal di tangan pakar.'],
                        ['title' => 'Pangkalan data rakan terasing', 'description' => 'Setiap rakan beroperasi dalam sempadan datanya sendiri.'],
                        ['title' => 'Pelbagai rakan & brand', 'description' => 'Satu penyebaran melayani banyak organisasi klinik.'],
                    ],
                    'faq' => [
                        ['question' => 'Adakah cadangan AI satu diagnosis?', 'answer' => 'Tidak. Cadangan AI hanya dipaparkan untuk pertimbangan dan mesti disahkan oleh operator atau penyemak yang berkelayakan.'],
                        ['question' => 'Bagaimana data rakan disimpan?', 'answer' => 'Setiap rakan mempunyai pangkalan data berasingan, jadi data kekal terasing.'],
                    ],
                    'seo' => [
                        'title'       => 'LBA Cloud — Integrated Clinical Intelligence',
                        'description' => 'Platform klinikal bersepadu untuk operasi makmal LBA.',
                    ],
                ],
            ];

            $base = self::blankLocale();
            $seed = $t[$locale] ?? [];

            return array_replace_recursive($base, $seed);
        };

        return [
            'locales' => ['id' => $mk('id'), 'en' => $mk('en'), 'ms' => $mk('ms')],
            'media'   => [],
        ];
    }
}
