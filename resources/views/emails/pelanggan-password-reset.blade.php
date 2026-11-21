<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="color: #087f80;">{{ __('messages.pelanggan_password_reset_subject') }}</h2>

    <p>{{ __('messages.pelanggan_password_reset_greeting', ['name' => $user->name]) }}</p>
    <p>{{ __('messages.pelanggan_password_reset_body') }}</p>

    <p style="font-size: 20px; font-weight: bold; letter-spacing: 2px; margin: 16px 0;">{{ $password }}</p>

    <p style="color: #6b7280; font-size: 12px;">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
</body>
</html>
