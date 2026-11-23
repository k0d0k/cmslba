@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $columns = [
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('audit.columns.created_at')],
            ['data' => 'event', 'name' => 'event', 'title' => __('audit.columns.event')],
            ['data' => 'user_name', 'name' => 'user_name', 'title' => __('audit.columns.user')],
            ['data' => 'description', 'name' => 'description', 'title' => __('audit.columns.description')],
            ['data' => 'detail', 'name' => 'detail', 'title' => __('common.detail'), 'orderable' => false, 'searchable' => false],
        ];
    @endphp

    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>{{ $title }}</span>
        </div>

        <div class="card-body border-b border-slate-100">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('audit.columns.event') }}</label>
                    <select name="event" class="form-input">
                        <option value="">{{ __('audit.all') }}</option>
                        @foreach($events as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['event'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('audit.from') }}</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('audit.to') }}</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-input">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('common.filter') }}</button>
                    <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary btn-sm">{{ __('common.reset') }}</a>
                </div>
            </form>
        </div>

        <div class="card-body !p-0">
            <table class="js-datatable w-full display"
                   data-dt-url="{{ route('admin.audit.data', $filters) }}"
                   data-dt-columns='@json($columns)'
                   data-dt-actions="0">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach($columns as $c)
                            <th>{{ $c['title'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Jejak Audit --}}
    <div id="audit-detail-modal" class="modal-overlay hidden" data-modal>
        <div class="modal-box w-full max-w-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                <h3 class="text-lg font-semibold text-slate-800">{{ __('audit.modal_title') }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 text-xl leading-none" data-modal-close>&times;</button>
            </div>

            <div id="audit-detail-loading" class="py-8 text-center text-slate-400">{{ __('audit.loading') }}</div>
            <dl id="audit-detail-body" class="hidden grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-3 text-sm"></dl>
        </div>
    </div>

    <script>
        (function () {
            const labels = @json(__('audit.fields'));
            const msgLoadFailed = @json(__('audit.load_failed'));

            const escapeHtml = (v) => String(v ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

            const row = (label, value, full = false) => {
                const span = full ? 'sm:col-span-3' : 'sm:col-span-1';
                return '<div class="' + span + '">'
                    + '<dt class="text-xs font-medium uppercase tracking-wide text-slate-400">' + escapeHtml(label) + '</dt>'
                    + '<dd class="mt-0.5 break-words text-slate-700">' + value + '</dd>'
                    + '</div>';
            };

            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.js-audit-detail');
                if (!btn) return;

                const modal = document.getElementById('audit-detail-modal');
                const loading = document.getElementById('audit-detail-loading');
                const body = document.getElementById('audit-detail-body');
                if (!modal) return;

                loading.classList.remove('hidden');
                body.classList.add('hidden');
                body.innerHTML = '';
                modal.classList.add('open');

                try {
                    const res = await fetch(btn.dataset.url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error(msgLoadFailed);
                    const d = await res.json();

                    let html = '';
                    html += row(labels.created_at, escapeHtml(d.created_at || '-'));
                    html += row(labels.event, '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium '
                        + (d.badge_class || 'bg-slate-100 text-slate-600') + '">' + escapeHtml(d.event_label || d.event) + '</span>');
                    html += row(labels.user, d.user_name
                        ? escapeHtml(d.user_name) + (d.user_email ? ' <span class="text-slate-400">&lt;' + escapeHtml(d.user_email) + '&gt;</span>' : '')
                        : '<span class="text-slate-400">-</span>');
                    html += row(labels.mitra_id, d.mitra_id ? escapeHtml(d.mitra_id) : '<span class="text-slate-400">-</span>');
                    html += row(labels.module, d.module ? escapeHtml(d.module) : '<span class="text-slate-400">-</span>');
                    html += row(labels.status_code, d.status_code ? escapeHtml(d.status_code) : '<span class="text-slate-400">-</span>');
                    html += row(labels.method, d.method ? escapeHtml(d.method) : '<span class="text-slate-400">-</span>');
                    html += row(labels.subject, d.subject ? escapeHtml(d.subject) : '<span class="text-slate-400">-</span>');
                    html += row(labels.route_name, d.route_name ? escapeHtml(d.route_name) : '<span class="text-slate-400">-</span>');
                    html += row(labels.ip, d.ip ? escapeHtml(d.ip) : '<span class="text-slate-400">-</span>');
                    html += '<div class="sm:col-span-3">'
                        + '<dt class="text-xs font-medium uppercase tracking-wide text-slate-400">' + labels.description + '</dt>'
                        + '<dd class="mt-0.5 break-words text-slate-700">' + escapeHtml(d.description || '-') + '</dd></div>';
                    html += row(labels.url, d.url
                        ? '<a href="' + escapeHtml(d.url) + '" target="_blank" rel="noopener" class="text-teal-600 hover:underline break-all">' + escapeHtml(d.url) + '</a>'
                        : '<span class="text-slate-400">-</span>', true);
                    html += '<div class="sm:col-span-3">'
                        + '<dt class="text-xs font-medium uppercase tracking-wide text-slate-400">' + labels.user_agent + '</dt>'
                        + '<dd class="mt-0.5 break-words text-slate-700">' + escapeHtml(d.user_agent || '-') + '</dd></div>';
                    if (d.properties) {
                        html += '<div class="sm:col-span-3">'
                            + '<dt class="text-xs font-medium uppercase tracking-wide text-slate-400">' + labels.properties + '</dt>'
                            + '<dd class="mt-0.5"><pre class="max-h-60 overflow-auto rounded-lg bg-slate-900 p-3 text-xs text-slate-100">'
                            + escapeHtml(JSON.stringify(d.properties, null, 2)) + '</pre></dd></div>';
                    }

                    body.innerHTML = html;
                } catch (err) {
                    body.innerHTML = '<div class="sm:col-span-3 text-rose-600">' + escapeHtml(err.message) + '</div>';
                } finally {
                    loading.classList.add('hidden');
                    body.classList.remove('hidden');
                }
            });
        })();
    </script>
@endsection
