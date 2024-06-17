@component('mail::message')
# Email Verified Successfully

ようこそ, {{ $user->name }}さん!

メールアドレスの確認が完了しました。<br>
アカウントへのログインができるようになりましたので<br>
ログイン画面よりログインしてください。

@component('mail::button', ['url' => route('login')])
ログイン画面へ
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent