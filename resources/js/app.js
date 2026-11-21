import './bootstrap';
import $ from 'jquery';
import Sortable from 'sortablejs';
import { createIcons, icons } from 'lucide';
import 'datatables.net-dt/css/dataTables.dataTables.css';

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
    document.querySelectorAll('[data-sidebar-toggle]').forEach((el) => {
        el.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            document.getElementById('sidebar')?.classList.toggle('hidden');
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

async function initDataTables() {
    if (!document.querySelector('.js-datatable')) return;
    const { default: DataTable } = await import('datatables.net-dt');

    document.querySelectorAll('.js-datatable').forEach((table) => {
        const url = table.dataset.url;
        let columns = [];
        try {
            columns = JSON.parse(table.dataset.columns || '[]');
        } catch (e) {
            columns = [];
        }

        const hasActions = table.dataset.actions === '1';

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
            ajax: url,
            columns: cols,
            order: [],
            pageLength: 10,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
                zeroRecords: 'Belum ada data',
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
    initDataTables();
    initMenuSortable();
});
