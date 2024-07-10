@extends ('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="login-form">
    <h2 class="verification-form__heading content__heading">
        メールアドレスの確認について
    </h2>
    <div class="verification-form__inner">
        <div class="verification-form__form">
            <div class="verification-form__group">
                <p class="verification-form__text">登録ありがとうございます！</p>
            </div>
            <div class="verification-form__group">
                <p>ログインするにはメールアドレスの確認が必要です。登録したメールアドレスにメールを送信していますので、メール内のリンクをクリックしてください。</p>
            </div>
            <div class="verification-form__group">
                <p>メールが届いていない場合は、以下のボタンをクリックして再送信画面よりメールの再送信をしてください。</p>
            </div>
            <a class="verification-form__button" href="{{ route('verification.resend') }}">メール再送画面へ</a>
        </div>
        <div class="verification-form__link">
            <p class="verification-form__link-more">すでにメール確認をしている方はこちらから</p>
            <a class="verification-form__link-login" href="/login">ログイン</a>
        </div>
    </div>
</div>

@endsection('content')