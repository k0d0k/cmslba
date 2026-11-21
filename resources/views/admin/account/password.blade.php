@extends('layouts.admin')

@section('title', __('common.change_password'))

@section('content')
    <div class="card max-w-xl">
        <div class="card-header">{{ __('common.change_password') }}</div>

        <form method="POST" action="{{ route('admin.account.password.update') }}" class="card-body space-y-4">
            @csrf

            <div>
                <label class="form-label">{{ __('common.current_password') }}</label>
                <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('common.new_password') }}</label>
                <input type="password" name="password" class="form-control" required autocomplete="new-password">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('common.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route('admin.account.profile') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
