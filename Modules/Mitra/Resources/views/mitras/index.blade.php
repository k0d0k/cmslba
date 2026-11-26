@extends('layouts.admin')

@section('title', __('mitra.multi_title'))


@section('content')
    @php
        $columns = [
            ['data' => 'logo',      'name' => 'logo', 'orderable' => false, 'searchable' => false],
            ['data' => 'name',      'name' => 'name'],
            ['data' => 'slug',      'name' => 'slug'],
            ['data' => 'db_name',   'name' => 'db_name'],
            ['data' => 'brand',     'name' => 'brand'],
            ['data' => 'langganan', 'name' => 'langganan'],
            ['data' => 'status',    'name' => 'status'],
        ];

        $hasActions = ($canUpdate ?? false) || ($canDelete ?? false);
    @endphp



    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>{{ __('mitra.list_title') }}</span>
            @if($canCreate ?? false)
                <a href="{{ route('admin.mitras.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> {{ __('common.add') }}</a>
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
                        <th>{{ __('common.logo') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('mitra.slug_frontpage') }}</th>
                        <th>{{ __('common.database') }}</th>
                        <th>{{ __('common.brand') }}</th>
                        <th>{{ __('common.subscription') }}</th>
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
