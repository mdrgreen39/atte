@extends ('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/resend-verification.css') }}">
@endsection

@section('content')
<div class="resend-form">
    <h2 class="resend-form__heading content__heading">
        確認メール再送信
    </h2>
    <div class="resend-form__inner">
        <form novalidate class="resend-form__form" action="{{ route('verification.resend.post') }}" method="post" novalidate>
            @csrf
            <div class="resend-form__group">
                <input class="resend-form__input" type='email' name="email" id="email" placeholder="メールアドレス" value="{{ old('email') }}" required>
                @if ($errors->any())
                <div class="resend-form__error-message">
                        @foreach ($errors->all() as $error)
                        {{ $error }}
                        @endforeach
                </div>
                @endif
            </div>
            <input class="resend-form__button" type="submit" value="送信">
        </form>
        <div class="resend-form__link">
            <p class="resend-form__link-more">アカウントをお持の方はこちらから</p>
            <a class="resend-form__link-register" href="/login">ログイン</a>
        </div>
    </div>
</div>

@endsection('content')