@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record ? route($routeName . '.update', $record->slug) : route($routeName . '.store');
    @endphp

    <div class="card max-w-3xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" class="card-body space-y-4">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            @foreach($fields as $f)
                @php
                    $name = $f['name'];
                    $type = $f['type'] ?? 'string';
                    $value = old($name, optional($record)->{$name});
                @endphp

                <div>
                    <label class="form-label">{{ $f['label'] ?? $name }}</label>

                    @if($type === 'text')
                        <textarea name="{{ $name }}" rows="3" class="form-control">{{ $value }}</textarea>

                    @elseif($type === 'boolean')
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="{{ $name }}" value="1" class="form-check" @checked($value)>
                            {{ $f['label'] ?? $name }}
                        </label>

                    @elseif($type === 'select')
                        <select name="{{ $name }}" class="form-control">
                            <option value="">-</option>
                            @foreach($f['options'] ?? [] as $optKey => $optLabel)
                                <option value="{{ $optKey }}" @selected((string) $value === (string) $optKey)>{{ $optLabel }}</option>
                            @endforeach
                        </select>

                    @elseif($type === 'relation')
                        @php
                            $relModel = $f['relation']['model'];
                            $relLabel = $f['relation']['label'] ?? 'name';
                            $relRows = class_exists($relModel) ? $relModel::query()->get() : collect();
                        @endphp
                        <select name="{{ $name }}" class="form-control">
                            <option value="">-</option>
                            @foreach($relRows as $row)
                                <option value="{{ $row->id }}" @selected((string) $value === (string) $row->id)>
                                    {{ $row->{$relLabel} }}
                                </option>
                            @endforeach
                        </select>

                    @elseif($type === 'date')
                        <input type="date" name="{{ $name }}"
                               value="{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '' }}"
                               class="form-control">

                    @elseif($type === 'datetime')
                        <input type="datetime-local" name="{{ $name }}"
                               value="{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '' }}"
                               class="form-control">

                    @elseif($type === 'integer')
                        <input type="number" name="{{ $name }}" value="{{ $value }}" class="form-control">

                    @elseif($type === 'decimal')
                        <input type="number" step="0.01" name="{{ $name }}" value="{{ $value }}" class="form-control">

                    @elseif($type === 'email')
                        <input type="email" name="{{ $name }}" value="{{ $value }}" class="form-control">

                    @else
                        <input type="text" name="{{ $name }}" value="{{ $value }}" class="form-control">
                    @endif

                    @error($name)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
