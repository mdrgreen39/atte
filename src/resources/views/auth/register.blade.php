@extends ('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register-form">
    <h2 class="register-form__heading content__heading">
        会員登録
    </h2>
    <div class="register-form__inner">
        <form class="register-form__form" action="/register" method="post" novalidate>
            @csrf
            <div class="register-form__group">
                <input class="register-form__input" type="text" name="name" id="name" placeholder="名前" value="{{ old('name') }}">
                <p class="register-form__error-message">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <input class="register-form__input" type="email" name="email" id="email" placeholder="メールアドレス" value="{{ old('email') }}">
                <p class="register-form__error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <input class="register-form__input" type="password" name="password" id="password" placeholder="パスワード">
                <p class="register-form__error-message">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation" placeholder="確認用パスワード">
                <p class="register-form__error-message">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <input class="register-form__button" type="submit" value="会員登録">
        </form>
        <div class="register-form__link">
            <p class="register-form__link-more">アカウントをお持ちの方はこちらから</p>
            <a class="register-form__link-login" href="/login">ログイン</a>
        </div>
    </div>
</div>
@endsection