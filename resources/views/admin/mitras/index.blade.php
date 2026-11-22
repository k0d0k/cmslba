@extends('layouts.admin')

@section('title', 'Multi Mitra')


@section('content')
    @php
        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            ['data' => 'name',        'name' => 'name'],
            ['data' => 'slug',        'name' => 'slug'],
            ['data' => 'db_name',     'name' => 'db_name'],
            ['data' => 'brand',       'name' => 'brand'],
            ['data' => 'langganan',   'name' => 'langganan'],
            ['data' => 'status',      'name' => 'status'],
            ['data' => 'actions',    'name' => 'actions', 'orderable' => false, 'searchable' => false],
        ];

        $hasActions = ($canUpdate ?? false) || ($canDelete ?? false);
    @endphp



    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>Daftar Mitra (Tenant)</span>
            @if($canCreate ?? false)
                <a href="{{ route('admin.mitras.create') }}" class="btn btn-primary btn-sm">{{ __('common.add') }}</a>
            @endif
        </div>

        <div class="card-body !p-0">
            <table class="js-datatable w-full display"
                   data-dt-url="{{ route('admin.mitras.data') }}"
                   data-dt-columns='@json($columns)'
                   data-dt-actions="{{ $hasActions ? '1' : '0' }}">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.name') }}</th>
                        <th>Slug (Frontpage)</th>
                        <th>Database</th>
                        <th>Brand</th>
                        <th>Langganan</th>
                        <th>{{ __('common.status') }}</th>
                        @if($hasActions)
                            <th>{{ __('common.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection
