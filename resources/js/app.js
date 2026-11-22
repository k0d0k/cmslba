import './bootstrap';
import $ from 'jquery';
import Sortable from 'sortablejs';
import Swal from 'sweetalert2';
import { createIcons, icons } from 'lucide';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'select2/dist/css/select2.min.css';

// Set jQuery ke window SEBELUM select2 dimuat agar UMD select2 menempel
// pada instance jQuery yang sama dengan $ (penting agar $.fn.select2 tersedia).
window.$ = window.jQuery = $;
window.Sortable = Sortable;
window.Swal = Swal;

// Dynamic import select2 setelah jQuery global tersedia.
const select2Module = await import('select2');
const select2Factory = select2Module.default ?? select2Module;
if (typeof select2Factory === 'function') {
    select2Factory($);
}

/**
 * Ganti elemen <i data-lucide="nama-ikon"> menjadi SVG.
 * Bisa dipanggil ulang setelah konten dinamis dirender.
 */
function initIcons(root = document) {
    createIcons({ icons, attrs: { 'stroke-width': 1.75 }, root });
}

window.initIcons = initIcons;

function initSidebar() {
    const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;
    const closeMobile = () => document.body.classList.remove('sidebar-open');

    // Tombol toggle: kolaps di desktop, buka overlay di mobile.
    document.querySelectorAll('[data-sidebar-toggle]').forEach((el) => {
        el.addEventListener('click', () => {
            if (isDesktop()) {
                document.body.classList.toggle('sidebar-collapsed');
            } else {
                document.body.classList.toggle('sidebar-open');
            }
        });
    });

    // Klik backdrop menutup sidebar mobile.
    document.getElementById('sidebar-backdrop')?.addEventListener('click', closeMobile);

    // Klik link di sidebar menutup overlay mobile.
    document.querySelectorAll('#sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (!isDesktop()) closeMobile();
        });
    });

    document.querySelectorAll('[data-dropdown-toggle]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.stopPropagation();
            const target = document.getElementById(el.dataset.dropdownToggle);
            document.querySelectorAll('[data-dropdown]').forEach((d) => {
                if (d !== target) d.classList.add('hidden');
            });
            target?.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown]').forEach((d) => d.classList.add('hidden'));
    });
}

function initDeletes() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-delete');
        if (!btn) return;

        const result = await window.Swal.fire({
            icon: 'warning',
            title: btn.dataset.title || 'Konfirmasi Hapus',
            text: btn.dataset.confirm || 'Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            focusCancel: true,
        });
        if (!result.isConfirmed) return;

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                    'Accept': 'application/json',
                },
            });
            if (res.ok) {
                await window.Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil dihapus.',
                    timer: 1400,
                    showConfirmButton: false,
                });
                window.location.reload();
            } else {
                window.Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus data.' });
            }
        } catch (err) {
            window.Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menghapus data.' });
        }
    });
}

function initConfirmForms() {
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form.js-confirm');
        if (!form) return;
        // Sudah dikonfirmasi sebelumnya -> lanjutkan submit.
        if (form.dataset.confirmed === '1') return;

        e.preventDefault();
        const result = await window.Swal.fire({
            icon: form.dataset.icon || 'question',
            title: form.dataset.title || 'Konfirmasi',
            text: form.dataset.confirm || 'Yakin ingin melanjutkan?',
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmText || 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: form.dataset.danger === '1' ? '#dc2626' : undefined,
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            focusCancel: true,
        });

        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.requestSubmit ? form.requestSubmit() : form.submit();
        }
    });
}

async function initDataTables() {
    if (!document.querySelector('.js-datatable')) return;
    const { default: DataTable } = await import('datatables.net-dt');
    // Ekstensi Responsive: kolom akan dilipat otomatis pada layar sempit.
    await import('datatables.net-responsive-dt');

    document.querySelectorAll('.js-datatable').forEach((table) => {
        // Catatan: DataTables v3 menggabungkan seluruh atribut data-* pada elemen
        // tabel ke opsi init-nya. Karena itu atribut konfigurasi kita memakai
        // prefiks "dt-" (data-dt-url / data-dt-columns / data-dt-actions) agar
        // tidak menimpa opsi bawaan seperti "columns".
        const url = table.dataset.dtUrl;
        let columns = [];
        try {
            columns = JSON.parse(table.dataset.dtColumns || '[]');
        } catch (e) {
            columns = [];
        }

        const hasActions = table.dataset.dtActions === '1';

        const cols = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            ...columns,
        ];
        if (hasActions) {
            cols.push({ data: 'actions', name: 'actions', orderable: false, searchable: false });
        }

        new DataTable(table, {
            processing: true,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: url,
            columns: cols,
            order: [],
            pageLength: 10,
            pagingType: 'simple_numbers',
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging',
            },
            language: {
                search: '',
                searchPlaceholder: 'Cari...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
                zeroRecords: 'Belum ada data',
                emptyTable: 'Belum ada data',
                processing: 'Memuat...',
            },
        });
    });
}

function initMenuSortable() {
    const el = document.getElementById('menu-tree');
    if (!el || !window.Sortable) return;

    const serialize = () => {
        const read = (list) => Array.from(list.children).map((li) => {
            const childList = li.querySelector(':scope > .submenu');
            return {
                id: li.dataset.id,
                children: childList ? read(childList) : [],
            };
        });
        return read(el);
    };

    const save = () => {
        fetch(el.dataset.reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
            },
            body: JSON.stringify({ tree: serialize() }),
        });
    };

    const make = (node) => window.Sortable.create(node, {
        group: 'menu',
        handle: '.drag-handle',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onEnd: () => {
            node.querySelectorAll(':scope > li > .submenu').forEach((sub) => make(sub));
            save();
        },
    });

    make(el);
    el.querySelectorAll('.submenu').forEach((sub) => make(sub));
}

function initSelect2(root = document) {
    if (!window.$ || typeof window.$.fn?.select2 !== 'function') return;
    root.querySelectorAll('select.js-select2').forEach((el) => {
        const $el = window.$(el);
        if ($el.data('select2')) return; // sudah di-init
        $el.select2({
            width: '100%',
            placeholder: el.dataset.placeholder || 'Pilih...',
            allowClear: el.dataset.allowClear === '1',
            language: {
                noResults: () => 'Tidak ada data yang cocok',
                searching: () => 'Mencari...',
                inputTooShort: () => 'Ketik untuk mencari',
            },
        });

        // Jika select berada dalam form dengan data-submit-on-change, otomatis
        // submit form saat pilihan berubah (mis. picker mitra → ?mitra=slug).
        const form = el.closest('form');
        if (form && form.dataset.submitOnChange === '1') {
            $el.on('change', function () {
                form.requestSubmit();
            });
        }
    });
}

window.initSelect2 = initSelect2;

/**
 * Modal preview gambar. Elemen pemicu cukup beri class `js-image-preview`
 * dan atribut `data-full-src` (URL gambar ukuran penuh). Bila tidak ada,
 * fallback ke `src` gambar pertama di dalam elemen tersebut.
 */
function initImagePreview() {
    const modal = document.getElementById('image-preview-modal');
    const img = document.getElementById('image-preview-img');
    const closeBtn = document.getElementById('image-preview-close');
    if (!modal || !img) return;

    const open = (src) => {
        if (!src) return;
        img.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };
    const close = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        img.src = '';
    };

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.js-image-preview');
        if (trigger) {
            e.preventDefault();
            const src = trigger.dataset.fullSrc || trigger.querySelector('img')?.src || trigger.src;
            open(src);
            return;
        }
        // Klik tombol tutup atau backdrop (bukan area gambar) -> tutup.
        if (e.target === closeBtn || closeBtn?.contains(e.target)) {
            close();
        } else if (e.target === modal) {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            close();
        }
    });
}

window.initImagePreview = initImagePreview;

/**
 * Modal generic (bukan image preview).
 *   - Pemicu: elemen dengan atribut `data-modal-target="<id-modal>"`
 *   - Penutup: elemen dengan `data-modal-close` (di dalam modal)
 *   - Modal terbuka = class `open` pada elemen dengan atribut `data-modal`
 */
function initModal() {
    document.addEventListener('click', (e) => {
        const openTrigger = e.target.closest('[data-modal-target]');
        if (openTrigger) {
            e.preventDefault();
            const id = openTrigger.dataset.modalTarget;
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('open');
            return;
        }

        const closeTrigger = e.target.closest('[data-modal-close]');
        if (closeTrigger) {
            e.preventDefault();
            closeTrigger.closest('[data-modal]')?.classList.remove('open');
            return;
        }

        // Klik backdrop (overlay) -> tutup.
        if (e.target.matches('[data-modal]')) {
            e.target.classList.remove('open');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal].open').forEach((m) => m.classList.remove('open'));
        }
    });
}

window.initModal = initModal;

// Jalankan inisialisasi. Karena select2 dimuat via dynamic import (await),
// module ini selesai setelah DOM siap; jadi jalankan langsung bila document
// sudah loaded, selain itu tunggu DOMContentLoaded. Tambah fallback window.load
// dan retry agar select2 tetap ter-init walau timing load bervariasi.
function boot() {
    initIcons();
    initSidebar();
    initDeletes();
    initConfirmForms();
    initDataTables();
    initMenuSortable();
    initSelect2();
    initImagePreview();
    initModal();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
window.addEventListener('load', () => initSelect2());
// Retry select2 beberapa kali untuk berjaga-jaga.
let retries = 0;
const retrySelect2 = () => {
    initSelect2();
    if (retries++ < 5) setTimeout(retrySelect2, 400);
};
setTimeout(retrySelect2, 300);
