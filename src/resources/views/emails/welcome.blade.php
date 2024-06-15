@component('mail::message')
# Email Verified Successfully

Hello, {{ $user->name }}!

Your email address has been successfully verified. You can now log in to your account.

@component('mail::button', ['url' => route('login')])
Log In
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent