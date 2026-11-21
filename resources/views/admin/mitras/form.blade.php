@extends('layouts.admin')

@section('title', $mitra ? 'Ubah Mitra' : 'Tambah Mitra')

@section('content')
    @php
        $action = $mitra ? route('admin.mitras.update', $mitra->slug) : route('admin.mitras.store');
    @endphp

    <div class="card max-w-2xl">
        <div class="card-header">{{ $mitra ? __('common.edit') : __('common.add') }} Mitra</div>

        <form method="POST" action="{{ $action }}" class="card-body space-y-4">
            @csrf
            @if($mitra)
                @method('PUT')
            @endif

            <div>
                <label class="form-label">{{ __('common.name') }} Mitra</label>
                <input type="text" name="name" value="{{ old('name', $mitra?->name) }}" class="form-control" required>
                @if($mitra)
                    <p class="text-xs text-slate-400 mt-1">
                        Database: <b>{{ $mitra->db_name }}</b> &middot;
                        Frontpage: <a class="underline" href="{{ route('frontpage.mitra', $mitra->slug) }}" target="_blank">{{ url('/' . $mitra->slug) }}</a>
                    </p>
                @endif
            </div>

            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $mitra?->slug) }}" class="form-control" placeholder="otomatis dari nama">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ __('common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $mitra?->email) }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">{{ __('common.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $mitra?->phone) }}" class="form-control">
                </div>
            </div>

            <div>
                <label class="form-label">{{ __('common.address') }}</label>
                <textarea name="address" rows="2" class="form-control">{{ old('address', $mitra?->address) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-control">
                        <option value="">-</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected((int) old('brand_id', $mitra?->brand_id) === $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Langganan</label>
                    <select name="langganan_id" class="form-control">
                        <option value="">-</option>
                        @foreach($subscriptions as $subscription)
                            <option value="{{ $subscription->id }}" @selected((int) old('langganan_id', $mitra?->langganan_id) === $subscription->id)>{{ $subscription->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
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
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-500">
                    Saat disimpan, sistem akan otomatis membuat database mitra
                    <b>lbacloud_mitra_&lt;uuid&gt;</b> dan menjalankan migrasi.
                </div>
            @endunless

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route('admin.mitras.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
