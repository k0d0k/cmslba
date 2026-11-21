@extends('layouts.admin')

@section('title', __('common.assign') . ' - ' . $role->name)

@section('content')
    @php $meta = config("lbacloud.roles.{$role->name}"); @endphp

    <div class="card">
        <div class="card-header flex items-center justify-between">
            <div>
                <div class="font-semibold">{{ $meta['label'] ?? $role->name }}</div>
                <code class="text-[11px] text-slate-400">{{ $role->name }}</code>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">{{ __('common.back') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.roles.assign.save', $role->id) }}">
            @csrf
            <div class="card-body p-0 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs uppercase text-slate-500 border-b border-slate-200 bg-slate-50">
                            <th class="px-4 py-3 text-left">Menu</th>
                            @foreach($abilities as $ability)
                                <th class="px-4 py-3 text-center">{{ ucfirst($ability) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.roles.assign-row', ['items' => $tree, 'level' => 0])
                    </tbody>
                </table>
            </div>

            <div class="card-body flex items-center gap-2 border-t border-slate-100">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
                <span class="text-xs text-slate-400 ml-auto">Centang kemampuan CRUD untuk tiap menu.</span>
            </div>
        </form>
    </div>
@endsection
