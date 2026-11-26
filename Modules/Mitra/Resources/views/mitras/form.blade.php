@extends('layouts.admin')

@section('title', $mitra ? __('mitra.edit') : __('mitra.add'))

@section('content')
    @php
        $action = $mitra ? route('admin.mitras.update', $mitra->slug) : route('admin.mitras.store');
        $initialBrandIds = old('brand_ids', []);
        $initialBrandIds = is_array($initialBrandIds) ? array_map('strval', $initialBrandIds) : [];
        $availableBrands = $mitra
            ? $brands->reject(fn ($brand) => $mitra->brands->contains('id', $brand->id))
            : $brands;
    @endphp

    <div class="card max-w-5xl">
        <div class="card-header">{{ $mitra ? __('mitra.edit') : __('mitra.add') }}</div>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card-body space-y-4">
            @csrf
            @if($mitra)
                @method('PUT')
            @endif

            <div>
                <label class="form-label">{{ __('mitra.logo') }}</label>
                @if($mitra?->logo)
                    <div class="mb-2">
                        <button type="button" class="js-image-preview inline-block" data-full-src="{{ \Illuminate\Support\Facades\Storage::url($mitra->logo) }}">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($mitra->logo) }}" alt="{{ __('mitra.logo') }}" class="h-16 rounded-lg object-contain border border-slate-200 bg-white p-1 cursor-pointer hover:opacity-80">
                        </button>
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*" class="form-control">
                @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">{{ __('mitra.logo_hint') }}</p>
            </div>

            <div>
                <label class="form-label">{{ __('mitra.form_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $mitra?->name) }}" class="form-control" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @if($mitra)
                    <p class="text-xs text-slate-400 mt-1">
                        {{ __('common.database') }}: <b>{{ $mitra->db_name }}</b> &middot;
                        Frontpage: <a class="underline" href="{{ route('frontpage.mitra', $mitra->slug) }}" target="_blank">{{ url('/' . $mitra->slug) }}</a>
                    </p>
                @endif
            </div>

            <div>
                <label class="form-label">{{ __('common.slug') }}</label>
                <input type="text" name="slug" value="{{ old('slug', $mitra?->slug) }}" class="form-control" placeholder="{{ __('mitra.slug_hint') }}">
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ __('common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $mitra?->email) }}" class="form-control">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('common.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $mitra?->phone) }}" class="form-control">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">{{ __('common.address') }}</label>
                <textarea name="address" rows="2" class="form-control">{{ old('address', $mitra?->address) }}</textarea>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="form-label">{{ __('common.skin') }}</label>
                    <select name="skin" class="form-control">
                        <option value="">-</option>
                        @foreach(config('lbacloud.skins') as $code => $sk)
                            <option value="{{ $code }}" @selected(old('skin', $mitra?->skin) === $code)>{{ $sk['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('common.language') }}</label>
                    <select name="locale" class="form-control">
                        @foreach(config('lbacloud.locales') as $code => $loc)
                            <option value="{{ $code }}" @selected(old('locale', $mitra?->locale ?? 'id') === $code)>{{ $loc['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('common.status') }}</label>
                    <select name="status" class="form-control">
                        @foreach(['pending', 'active', 'suspended'] as $st)
                            <option value="{{ $st }}" @selected(old('status', $mitra?->status ?? 'active') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @unless($mitra)
            <div class="card max-w-5xl mb-5">
            <div class="card-header">{!! __('mitra.subscription_initial') !!}</div>
                <section class="border border-slate-200 rounded-lg p-4 space-y-4">
                    <h3 class="font-semibold text-slate-700">{!! __('mitra.subscription_initial') !!}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <div class="sm:col-span-2">
                            <label class="form-label">{{ __('mitra.package') }}</label>
                            <select name="initial_subscription_id" class="form-control">
                                <option value="">{{ __('mitra.no_package') }}</option>
                                @foreach($subscriptions as $subscription)
                                    <option value="{{ $subscription->id }}" @selected(old('initial_subscription_id') === $subscription->id)>
                                        {{ $subscription->name }} ({{ $subscription->billing_interval }})
                                    </option>
                                @endforeach
                            </select>
                            @error('initial_subscription_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('mitra.seats') }}</label>
                            <input type="number" name="initial_seats" min="1" value="{{ old('initial_seats', 1) }}" class="form-control">
                            @error('initial_seats') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">{{ __('mitra.attach_brand') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($brands as $brand)
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="brand_ids[]" value="{{ $brand->id }}" class="form-check"
                                           @checked(in_array((string) $brand->id, $initialBrandIds, true))>
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('brand_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('brand_ids.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div class="mt-3">
                            <label class="form-label">{{ __('mitra.primary_brand') }}</label>
                            <select name="primary_brand_id" class="form-control">
                                <option value="">{{ __('mitra.primary_brand_hint') }}</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((string) old('primary_brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-500">
                    {{ __('mitra.provision_note_1') }}
                    <b>lbacloud_mitra_&lt;uuid&gt;</b> {{ __('mitra.provision_note_2') }}
                </div>
                </div>
            @endunless

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> {{ __('common.save') }}</button>
                <a href="{{ route('admin.mitras.index') }}" class="btn btn-secondary"><i data-lucide="x"></i> {{ __('common.cancel') }}</a>
            </div>
        </form>
        </div>
        
        @if($mitra)
    <div class="card max-w-5xl mb-5">
        <div class="card-header">{!! __('mitra.subscription_mitra') !!}</div>
             <section class="border-t border-slate-200 p-4 space-y-4">
                <form method="POST" action="{{ route('admin.mitras.subscriptions.store', $mitra) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="form-label">{{ __('mitra.package') }}</label>
                        <select name="subscriptions_id" class="form-control" required>
                            <option value="">{{ __('mitra.select_package') }}</option>
                            @foreach($subscriptions as $subscription)
                                <option value="{{ $subscription->id }}" @selected(old('subscriptions_id') === $subscription->id)>
                                    {{ $subscription->name }} ({{ $subscription->billing_interval }})
                                </option>
                            @endforeach
                        </select>
                        @error('subscriptions_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="min-w-0 flex-1">
                            <label class="form-label">{{ __('mitra.seats') }}</label>
                            <input type="number" name="seats" min="1" value="{{ old('seats', 1) }}" class="form-control" required>
                            @error('seats') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary whitespace-nowrap">{{ __('mitra.subscribe_mitra') }}</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-left p-3">{{ __('mitra.col_package') }}</th>
                                <th class="text-left p-3">{{ __('mitra.col_status') }}</th>
                                <th class="text-left p-3">{{ __('mitra.col_seats') }}</th>
                                <th class="text-left p-3">{{ __('mitra.col_period_end') }}</th>
                                <th class="text-left p-3">{{ __('mitra.col_reference') }}</th>
                                <th class="text-left p-3">{{ __('mitra.col_action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($mitra->subscriptions as $mitraSubscription)
                                <tr>
                                    <td class="p-3">{{ $mitraSubscription->subscription?->name ?? '-' }}</td>
                                    <td class="p-3">{{ ['trialing' => __('mitra.status_trialing'), 'cancelled' => __('mitra.status_cancelled'), 'active' => __('mitra.status_active'), 'pending' => __('mitra.status_pending'), 'ended' => __('mitra.status_ended')][$mitraSubscription->status] ?? ucfirst($mitraSubscription->status) }}</td>
                                    <td class="p-3">{{ $mitraSubscription->seats }}</td>
                                    <td class="p-3">{{ $mitraSubscription->current_period_end?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="p-3 font-mono text-xs">{{ $mitraSubscription->reference }}</td>
                                    <td class="p-3">
                                        @if(in_array($mitraSubscription->status, ['cancelled', 'ended'], true))
                                            <form method="POST" action="{{ route('admin.mitras.subscriptions.activate', [$mitra, $mitraSubscription]) }}">
                                                @csrf
                                                <button class="btn btn-secondary btn-sm" type="submit">{{ __('mitra.activate') }}</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.mitras.subscriptions.cancel', [$mitra, $mitraSubscription]) }}" class="js-confirm" data-title="{{ __('mitra.cancel_title') }}" data-confirm="{{ __('mitra.cancel_confirm') }}" data-confirm-text="{{ __('mitra.cancel_confirm_yes') }}" data-danger="1">
                                                @csrf
                                                <button class="btn btn-secondary btn-sm" type="submit">{{ __('mitra.cancel') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-4 text-center text-slate-500">{{ __('mitra.no_history') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
    </div>
    <div class="card max-w-5xl mb-5">
        <div class="card-header">{{ __('common.brand') }}</div>
            <section class="border-t border-slate-200 p-4 space-y-4">
                <form method="POST" action="{{ route('admin.mitras.brands.store', $mitra) }}" class="grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-3 items-end">
                    @csrf
                    <div>
                        <label class="form-label">{{ __('mitra.attach_brand') }}</label>
                        <select name="brand_id" class="form-control" required>
                            <option value="">{{ __('mitra.choose_brand') }}</option>
                            @foreach($availableBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 pb-2">
                        <input type="checkbox" name="is_primary" value="1" class="form-check">
                        {{ __('mitra.primary_brand') }}
                    </label>
                    <button type="submit" class="btn btn-primary">{{ __('mitra.attach_brand') }}</button>
                </form>

                <div class="divide-y divide-slate-100 border-t border-slate-100">
                    @forelse($mitra->brands as $brand)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1">
                                <div class="font-medium text-slate-800">{{ $brand->name }}</div>
                                <div class="text-sm text-slate-500">
                                    {{ in_array(strtolower((string) $brand->is_active), ['1', 'active', 'yes', 'true'], true) ? __('mitra.brand_active') : __('mitra.brand_inactive') }}
                                    @if($brand->pivot->is_primary) &middot; {{ __('mitra.primary_brand') }} @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @unless($brand->pivot->is_primary)
                                    <form method="POST" action="{{ route('admin.mitras.brands.primary', [$mitra, $brand]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">{{ __('mitra.make_primary') }}</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.mitras.brands.destroy', [$mitra, $brand]) }}" class="js-confirm" data-title="{{ __('mitra.detach_title') }}" data-confirm="{{ __('mitra.detach_confirm') }}" data-confirm-text="{{ __('mitra.detach_confirm_yes') }}" data-danger="1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm">{{ __('mitra.detach') }}</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-slate-500">{{ __('mitra.no_brand') }}</p>
                    @endforelse
                </div>
            </section>
        @endif
    </div>
@endsection
