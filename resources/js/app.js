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
// Seluruh koleksi ikon lucide (PascalCase) untuk icon picker.
window.lucideIcons = icons;

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

    // Dropdown generik: tombol `[data-dropdown-toggle]` + panel `[data-dropdown]`.
    // Memakai event delegation agar tetap berfungsi pada konten dinamis
    // (mis. tombol aksi per baris DataTables yang dirender server-side).
    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-dropdown-toggle]');
        if (toggle) {
            const target = document.getElementById(toggle.dataset.dropdownToggle);
            document.querySelectorAll('[data-dropdown]').forEach((d) => {
                if (d !== target) d.classList.add('hidden');
            });
            target?.classList.toggle('hidden');
            return;
        }

        // Klik di dalam panel dropdown: jangan tutup (mis. checkbox pemilih kolom).
        if (e.target.closest('[data-dropdown]')) return;

        // Klik di luar dropdown: tutup semua.
        document.querySelectorAll('[data-dropdown]').forEach((d) => d.classList.add('hidden'));
    });
}

/**
 * Pemilih group menu pada sidebar. Hanya item dari group terpilih yang tampil
 * agar daftar menu tidak memanjang. Prioritas pilihan: group dari halaman aktif
 * (data-active) -> pilihan tersimpan (localStorage) -> group pertama.
 */
function initSidebarGroupNav() {
    const select = document.getElementById('sidebar-group-select');
    const wrap = document.getElementById('sidebar-groups');
    if (!select || !wrap) return;

    const panels = Array.from(wrap.querySelectorAll('[data-sidebar-group]'));
    const keys = panels.map((p) => p.dataset.sidebarGroup);
    if (!keys.length) return;

    const storeKey = 'sidebar-group';

    const show = (key) => {
        panels.forEach((p) => p.classList.toggle('hidden', p.dataset.sidebarGroup !== key));
        select.value = key;
    };

    let stored = null;
    try { stored = localStorage.getItem(storeKey); } catch (e) { stored = null; }

    const active = select.dataset.active;
    const initial = keys.includes(active) ? active
        : (keys.includes(stored) ? stored : keys[0]);

    show(initial);

    select.addEventListener('change', () => {
        show(select.value);
        try { localStorage.setItem(storeKey, select.value); } catch (e) { /* abaikan */ }
    });
}

window.initSidebarGroupNav = initSidebarGroupNav;

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

/**
 * Terjemahan kontrol DataTables. Diambil dari `window.LB.dt` yang dikirim
 * layout sesuai locale aktif, dengan fallback agar tetap aman bila kosong.
 */
function dataTablesLanguage() {
    const t = (window.LB && window.LB.dt) || {};

    return {
        search: '',
        searchPlaceholder: t.searchPlaceholder ?? 'Cari...',
        lengthMenu: t.lengthMenu ?? 'Tampilkan _MENU_ data',
        info: t.info ?? 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        infoEmpty: t.infoEmpty ?? 'Tidak ada data',
        infoFiltered: t.infoFiltered ?? '(difilter dari _MAX_ total data)',
        paginate: { previous: t.previous ?? 'Sebelumnya', next: t.next ?? 'Berikutnya' },
        zeroRecords: t.zeroRecords ?? 'Belum ada data',
        emptyTable: t.emptyTable ?? 'Belum ada data',
        processing: t.processing ?? 'Memuat...',
    };
}

/**
 * Tombol "Pilih Kolom" (column visibility) di kanan atas area DataTables.
 * Dibuat generik supaya tersedia otomatis pada semua tabel (.js-datatable).
 * Kolom nomor (#) dan kolom aksi tetap selalu tampil.
 */
function initColumnToggle(dt, table, dataColCount) {
    if (!dataColCount) return;

    const t = (window.LB && window.LB.dt) || {};
    const label = t.columns ?? 'Kolom';

    const headerCells = Array.from(table.tHead?.rows?.[0]?.cells ?? []);
    const items = [];
    for (let i = 1; i <= dataColCount; i++) {
        items.push({ idx: i, title: (headerCells[i]?.textContent || '').trim() || ('#' + i) });
    }

    const ddId = 'dt-colvis-' + Math.random().toString(36).slice(2, 8);

    const wrap = document.createElement('div');
    wrap.className = 'dt-colvis relative inline-block text-left ml-2';
    wrap.innerHTML = `
        <button type="button" class="btn btn-sm btn-secondary" data-dropdown-toggle="${ddId}" title="${label}">
            <i data-lucide="columns-3"></i><span class="hidden sm:inline">${label}</span>
        </button>
        <div id="${ddId}" data-dropdown class="hidden absolute right-0 mt-1 w-56 card p-2 z-40 max-h-72 overflow-auto text-left">
            <div class="flex items-center justify-between gap-2 px-2 pb-1.5 mb-1 border-b border-slate-100">
                <button type="button" class="js-colvis-all text-xs font-medium text-teal-600 hover:underline">${t.selectAll ?? 'Pilih semua'}</button>
                <button type="button" class="js-colvis-clear text-xs font-medium text-slate-500 hover:underline">${t.clear ?? 'Bersihkan'}</button>
            </div>
            ${items.map((it) => `
                <label class="flex items-center gap-2 px-2 py-1.5 rounded text-sm text-slate-600 hover:bg-slate-100 cursor-pointer">
                    <input type="checkbox" class="form-check js-colvis-toggle" data-col="${it.idx}" checked>
                    <span class="truncate">${it.title}</span>
                </label>`).join('')}
        </div>`;

    const checkboxes = Array.from(wrap.querySelectorAll('.js-colvis-toggle'));

    const setAll = (visible) => {
        checkboxes.forEach((cb) => {
            cb.checked = visible;
            dt.column(parseInt(cb.dataset.col, 10)).visible(visible);
        });
    };

    checkboxes.forEach((cb) => {
        cb.addEventListener('change', () => {
            dt.column(parseInt(cb.dataset.col, 10)).visible(cb.checked);
        });
    });

    wrap.querySelector('.js-colvis-all')?.addEventListener('click', () => setAll(true));
    wrap.querySelector('.js-colvis-clear')?.addEventListener('click', () => setAll(false));

    // Sisipkan di sebelah kanan kotak pencarian (kanan atas area DataTables).
    const search = dt.table().container().querySelector('.dt-search');
    if (search && search.parentNode) {
        search.parentNode.appendChild(wrap);
    } else {
        dt.table().container().appendChild(wrap);
    }

    if (window.initIcons) window.initIcons(wrap);
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

        const dt = new DataTable(table, {
            processing: true,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: url,
            columns: cols,
            order: [],
            pageLength: 10,
            // Rekonversi ikon lucide pada sel aksi setiap tabel selesai digambar.
            drawCallback: () => { if (window.initIcons) window.initIcons(table); },
            pagingType: 'simple_numbers',
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging',
            },
            language: dataTablesLanguage(),
        });

        // Tombol pemilih kolom di kanan atas (semua menu yang memakai DataTables).
        initColumnToggle(dt, table, columns.length);
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

/**
 * Icon picker: memilih ikon lewat grid + pencarian, tanpa perlu mengetik nama
 * ikon. Markup memakai partial `layouts.partials.icon-picker`:
 *   - `[data-icon-picker]`            : pembungkus (position: relative)
 *   - `[data-icon-picker-input]`      : input hidden yang nilainya dikirim form
 *   - `[data-icon-picker-toggle]`     : tombol pemicu
 *   - `[data-icon-picker-panel]`      : panel dropdown
 *   - `[data-icon-picker-search]`     : input pencarian
 *   - `[data-icon-picker-grid]`       : wadah grid ikon
 */
function initIconPickers(root = document) {
    const all = window.lucideIcons || {};

    const toPascal = (s) => s.split('-').map((w) => (w ? w.charAt(0).toUpperCase() + w.slice(1) : w)).join('');
    const toKebab = (s) => s
        .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
        .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
        .toLowerCase();

    // Hanya ikon yang namanya bisa di-resolve balik oleh createIcons().
    const names = Object.keys(all)
        .map((name) => toKebab(name))
        .filter((kebab) => all[toPascal(kebab)])
        .sort();

    root.querySelectorAll('[data-icon-picker]').forEach((picker) => {
        if (picker.dataset.init === '1') return;
        picker.dataset.init = '1';

        const input = picker.querySelector('[data-icon-picker-input]');
        const toggle = picker.querySelector('[data-icon-picker-toggle]');
        const panel = picker.querySelector('[data-icon-picker-panel]');
        const search = picker.querySelector('[data-icon-picker-search]');
        const grid = picker.querySelector('[data-icon-picker-grid]');
        const label = picker.querySelector('[data-icon-picker-label]');
        const preview = picker.querySelector('[data-icon-picker-preview]');

        let rendered = false;

        const markSelected = () => {
            grid.querySelectorAll('.icon-picker-item').forEach((item) => {
                item.classList.toggle('is-selected', item.dataset.iconName === input.value);
            });
        };

        const render = () => {
            if (rendered) return;
            rendered = true;
            const frag = document.createDocumentFragment();
            names.forEach((name) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'icon-picker-item';
                btn.dataset.iconName = name;
                btn.title = name;
                btn.innerHTML = '<i data-lucide="' + name + '"></i>';
                frag.appendChild(btn);
            });
            grid.appendChild(frag);
            if (window.initIcons) window.initIcons(grid);
            markSelected();
        };

        const filter = () => {
            const q = search.value.trim().toLowerCase();
            let visible = 0;
            grid.querySelectorAll('.icon-picker-item').forEach((item) => {
                const match = !q || item.dataset.iconName.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            let empty = grid.querySelector('.icon-picker-empty');
            if (!visible) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'icon-picker-empty';
                    empty.textContent = search.dataset.emptyText || 'Ikon tidak ditemukan';
                    grid.appendChild(empty);
                }
                empty.style.display = '';
            } else if (empty) {
                empty.style.display = 'none';
            }
        };

        const close = () => panel.classList.add('hidden');
        const open = () => {
            render();
            panel.classList.remove('hidden');
            search.value = '';
            filter();
            search.focus();
        };

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.contains('hidden') ? open() : close();
        });

        search.addEventListener('input', filter);

        grid.addEventListener('click', (e) => {
            const item = e.target.closest('.icon-picker-item');
            if (!item) return;
            input.value = item.dataset.iconName;
            if (label) label.textContent = item.dataset.iconName;
            if (preview) {
                preview.innerHTML = '<i data-lucide="' + item.dataset.iconName + '"></i>';
                if (window.initIcons) window.initIcons(preview);
            }
            markSelected();
            close();
        });

        document.addEventListener('click', (e) => {
            if (!picker.contains(e.target)) close();
        });
    });
}

window.initIconPickers = initIconPickers;

/**
 * Editor website/frontpage: tab bahasa + baris berulang (layanan/fitur/FAQ/media).
 * Baris baru dikloning dari <template data-row-template> dengan placeholder
 * `__INDEX__` diganti nilai unik.
 */
function initWebsiteBuilder() {
    const form = document.getElementById('wt-form');
    if (!form) return;

    // Tab bahasa.
    form.querySelectorAll('[data-wt-tab]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const code = btn.dataset.wtTab;
            form.querySelectorAll('[data-wt-tab]').forEach((b) => b.classList.toggle('active', b === btn));
            form.querySelectorAll('[data-wt-panel]').forEach((p) => p.classList.toggle('active', p.dataset.wtPanel === code));
        });
    });

    // Tambah baris.
    form.querySelectorAll('[data-repeat-add]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const container = document.getElementById(btn.dataset.repeatAdd);
            const tpl = container?.querySelector('template[data-row-template]');
            const rows = container?.querySelector('[data-repeat-rows]');
            if (!tpl || !rows) return;

            const idx = 'r' + Date.now() + Math.floor(Math.random() * 1000);
            const wrap = document.createElement('div');
            wrap.innerHTML = tpl.innerHTML.replace(/__INDEX__/g, idx).trim();
            rows.appendChild(wrap.firstElementChild);
            if (window.initIcons) window.initIcons(rows);
        });
    });

    // Hapus baris (delegasi).
    form.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-repeat-remove]');
        if (btn) {
            btn.closest('[data-repeat-row]')?.remove();
        }
    });
}

window.initWebsiteBuilder = initWebsiteBuilder;

// Jalankan inisialisasi. Karena select2 dimuat via dynamic import (await),
// module ini selesai setelah DOM siap; jadi jalankan langsung bila document
// sudah loaded, selain itu tunggu DOMContentLoaded. Tambah fallback window.load
// dan retry agar select2 tetap ter-init walau timing load bervariasi.
function boot() {
    initIcons();
    initSidebar();
    initSidebarGroupNav();
    initDeletes();
    initConfirmForms();
    initDataTables();
    initMenuSortable();
    initSelect2();
    initImagePreview();
    initModal();
    initIconPickers();
    initWebsiteBuilder();
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
