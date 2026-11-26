@extends('layouts.admin')

@section('title', __('common.profile'))

@section('content')
    <div class="card max-w-2xl">
        <div class="card-header">{{ __('common.profile') }}</div>

        <form method="POST" action="{{ route('admin.account.profile.update') }}" enctype="multipart/form-data" class="card-body space-y-4">
            @csrf

            <div>
                <label class="form-label">{{ __('common.picture') }}</label>
                <div class="flex items-center gap-4">
                    @if($user->avatar)
                        <button type="button" class="js-image-preview inline-block" data-full-src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="" class="w-16 h-16 rounded-full object-cover border border-slate-200 cursor-pointer hover:opacity-80">
                        </button>
                    @else
                        <span class="w-16 h-16 rounded-full grid place-items-center text-white text-xl font-semibold" style="background: var(--color-primary)">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                    <input type="file" name="picture" accept="image/*" class="form-control">
                </div>
                @error('picture')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ __('common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">{{ __('common.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">{{ __('common.language') }}</label>
                    <select name="locale" class="form-control">
                        @foreach(config('lbacloud.locales') as $code => $loc)
                            <option value="{{ $code }}" @selected(old('locale', $user->locale ?? 'id') === $code)>{{ $loc['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('common.skin') }}</label>
                    <select name="skin" class="form-control">
                        @foreach(\Modules\Setting\Models\Skin::where('is_active', true)->orderBy('id')->get() as $sk)
                            <option value="{{ $sk->code }}" @selected(old('skin', $user->skin ?? 'default') === $sk->code)>{{ $sk->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> {{ __('common.save') }}</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i data-lucide="x"></i> {{ __('common.cancel') }}</a>
                <a href="{{ route('admin.account.password') }}" class="btn btn-secondary ml-auto">{{ __('common.change_password') }}</a>
            </div>
        </form>
    </div>
@endsection
