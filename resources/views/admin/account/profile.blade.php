@extends('layouts.admin')

@section('title', __('common.profile'))

@section('content')
    <div class="card max-w-2xl">
        <div class="card-header">{{ __('common.profile') }}</div>

        <form method="POST" action="{{ route('admin.account.profile.update') }}" class="card-body space-y-4">
            @csrf

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
                        @foreach(\App\Models\Skin::where('is_active', true)->orderBy('id')->get() as $sk)
                            <option value="{{ $sk->code }}" @selected(old('skin', $user->skin ?? 'default') === $sk->code)>{{ $sk->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
                <a href="{{ route('admin.account.password') }}" class="btn btn-secondary ml-auto">{{ __('common.change_password') }}</a>
            </div>
        </form>
    </div>
@endsection
