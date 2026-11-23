@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record
            ? route($routeName . '.update', $record->getRouteKey())
            : route($routeName . '.store');
        $selectedModules = old('module_keys', $record?->module_keys ?? []);
        $selectedModules = is_array($selectedModules) ? $selectedModules : [];
        $intervals = [
            'monthly' => __('subscription.interval_monthly'),
            'yearly' => __('subscription.interval_yearly'),
            'weekly' => __('subscription.interval_weekly'),
            'daily' => __('subscription.interval_daily'),
        ];
        $currencies = ['IDR' => 'IDR', 'USD' => 'USD'];
    @endphp

    <div class="card max-w-5xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" class="card-body space-y-4">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ __('common.code') }}</label>
                    <input type="text" name="code" value="{{ old('code', $record?->code) }}" class="form-control" required>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $record?->name) }}" class="form-control" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('common.status') }}</label>
                    <select name="status" class="form-control" required>
                        @foreach(['active' => __('common.active'), 'inactive' => __('common.inactive')] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $record?->status ?? 'active') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_billing_period') }}</label>
                    <select name="billing_interval" class="form-control" required>
                        @foreach($intervals as $value => $label)
                            <option value="{{ $value }}" @selected(old('billing_interval', $record?->billing_interval ?? 'monthly') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('billing_interval') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">{{ __('common.description') }}</label>
                <textarea name="description" rows="2" maxlength="255" class="form-control">{{ old('description', $record?->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div>
                    <label class="form-label">{{ __('subscription.form_price_amount') }}</label>
                    <input type="number" name="price_amount" min="0" step="0.01" value="{{ old('price_amount', $record?->price_amount ?? '0.00') }}" class="form-control" required>
                    @error('price_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_currency') }}</label>
                    <select name="currency" class="form-control" required>
                        @foreach($currencies as $value => $label)
                            <option value="{{ $value }}" @selected(old('currency', $record?->currency ?? 'IDR') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_trial_days') }}</label>
                    <input type="number" name="trial_days" min="0" value="{{ old('trial_days', $record?->trial_days ?? 0) }}" class="form-control" required>
                    @error('trial_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_seat_limit') }}</label>
                    <input type="number" name="seat_limit" min="1" value="{{ old('seat_limit', $record?->seat_limit) }}" class="form-control">
                    @error('seat_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('subscription.form_sina_credit') }}</label>
                    <input type="number" name="sina_credit_per_patient_per_period" min="0" value="{{ old('sina_credit_per_patient_per_period', $record?->sina_credit_per_patient_per_period ?? 0) }}" class="form-control" required>
                    @error('sina_credit_per_patient_per_period') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1 text-xs text-slate-500">
                <p>{{ __('subscription.unlimited_hint') }}</p>
                <p>{{ __('subscription.credit_note') }}</p>
            </div>

            <fieldset>
                <legend class="form-label mb-1">{{ __('subscription.licensed_modules') }}</legend>
                <p class="text-xs text-slate-500 mb-2">{{ __('subscription.licensed_hint') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                    @foreach($modules as $key => $label)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="module_keys[]" value="{{ $key }}" class="form-check"
                                   @checked(in_array((string) $key, array_map('strval', $selectedModules), true))>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('module_keys') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('module_keys.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </fieldset>

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
