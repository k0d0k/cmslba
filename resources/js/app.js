import './bootstrap';
import $ from 'jquery';
import Sortable from 'sortablejs';
import { createIcons, icons } from 'lucide';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

window.$ = window.jQuery = $;
window.Sortable = Sortable;

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
        if (!confirm('Yakin ingin menghapus data ini?')) return;

        const res = await fetch(btn.dataset.url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Gagal menghapus data.');
        }
    });
}

function initConfirmForms() {
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form.js-confirm');
        if (!form) return;
        if (!confirm(form.dataset.confirm || 'Yakin?')) e.preventDefault();
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

document.addEventListener('DOMContentLoaded', () => {
    initIcons();
    initSidebar();
    initDeletes();
    initConfirmForms();
    initDataTables();
    initMenuSortable();
});
