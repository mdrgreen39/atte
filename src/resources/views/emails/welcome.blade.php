@component('mail::message')
# Email Verified Successfully

ようこそ、 {{ $user->name }}さん！

メールアドレスの確認が完了しました。<br>
アカウントへのログインが可能になりましたので<br>
ログイン画面からログインしてください。

@component('mail::button', ['url' => route('login')])
ログイン画面へ
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent