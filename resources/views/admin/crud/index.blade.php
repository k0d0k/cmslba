@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $tableFields = collect($fields)->reject(fn ($f) => !empty($f['hide_in_table']))->values();
        $columns = $tableFields->map(fn ($f) => ['data' => $f['name'], 'name' => $f['name']])->all();
        $hasActions = $canUpdate || $canDelete;
    @endphp

    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>{{ $title }}</span>
            @if($canCreate)
                <a href="{{ route($routeName . '.create') }}" class="btn btn-primary btn-sm">{{ __('common.add') }}</a>
            @endif
        </div>

        <div class="card-body !p-0">
            <table class="js-datatable w-full display"
                   data-dt-url="{{ route($routeName . '.data') }}"
                   data-dt-columns='@json($columns)'
                   data-dt-actions="{{ $hasActions ? '1' : '0' }}">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach($tableFields as $f)
                            <th>{{ $f['label'] ?? $f['name'] }}</th>
                        @endforeach
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
