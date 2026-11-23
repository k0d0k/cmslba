@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record ? route($routeName . '.update', $record->getRouteKey()) : route($routeName . '.store');
    @endphp

    <div class="card max-w-3xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card-body space-y-4">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            @foreach($fields as $f)
                @php
                    $type = $f['type'] ?? 'string';
                @endphp

                @if($type === 'section')
                    <div class="pt-3 border-t border-slate-100 first:border-0 first:pt-0">
                        <h3 class="text-sm font-semibold text-slate-700">{{ $f['title'] ?? '' }}</h3>
                        @if(!empty($f['text']))
                            <p class="text-xs text-slate-400 mt-1">{{ $f['text'] }}</p>
                        @endif
                    </div>
                    @continue
                @endif

                @php
                    $name = $f['name'];
                    $value = old($name, optional($record)->{$name} ?? ($f['default'] ?? null));
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
                        <select name="{{ $name }}" class="form-control" @disabled($f['disabled'] ?? false)>
                            <option value="">-</option>
                            @foreach($f['options'] ?? [] as $optKey => $optLabel)
                                <option value="{{ $optKey }}" @selected((string) $value === (string) $optKey)>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                        @if($f['disabled'] ?? false)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endif

                    @elseif($type === 'relation')
                        @php
                            $relModel = $f['relation']['model'];
                            $relLabel = $f['relation']['label'] ?? 'name';
                            $relWith = $f['relation']['with'] ?? [];
                            $relQuery = class_exists($relModel) ? $relModel::query() : null;
                            if ($relQuery && !empty($relWith)) {
                                $relQuery->with($relWith);
                            }
                            $relRows = $relQuery ? $relQuery->get() : collect();
                        @endphp
                        <select name="{{ $name }}" class="form-control">
                            <option value="">-</option>
                            @foreach($relRows as $row)
                                <option value="{{ $row->id }}" @selected((string) $value === (string) $row->id)>
                                    {{ $row->{$relLabel} }}
                                </option>
                            @endforeach
                        </select>

                    @elseif($type === 'image')
                        @if($value)
                            <div class="mb-2">
                                <button type="button" class="js-image-preview inline-block" data-full-src="{{ \Illuminate\Support\Facades\Storage::url($value) }}">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($value) }}" alt="" class="w-20 h-20 rounded-lg object-cover border border-slate-200 cursor-pointer hover:opacity-80">
                                </button>
                            </div>
                        @endif
                        <input type="file" name="{{ $name }}" accept="image/*" class="form-control">

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

                    @elseif($type === 'password')
                        <input type="password" name="{{ $name }}" value="" class="form-control" autocomplete="new-password">
                        @if($record && empty($f['hint']))
                            <p class="text-slate-400 text-xs mt-1">Biarkan kosong bila tidak ingin mengganti password.</p>
                        @endif

                    @elseif($type === 'secret')
                        <input type="password" name="{{ $name }}" value="" class="form-control" autocomplete="new-password" placeholder="••••••••">

                    @else
                        <input type="text" name="{{ $name }}" value="{{ $value }}" class="form-control">
                    @endif

                    @if(!empty($f['hint']))
                        <p class="text-slate-400 text-xs mt-1">{{ $f['hint'] }}</p>
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
