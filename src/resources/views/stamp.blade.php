@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp.css') }}">
@endsection

@section('nav')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a href="/">ホーム</a></li>
        <li class="header-nav__item"><a href="/attendance">日付一覧</a></li>
        <li class="header-nav__item">
            <form class="header-nav__button" action="/logout" method="post">
                @csrf
                <button class="header-nav__button-submit" type="submit">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
@endsection

@section('content')
<div class="stamp-form">
    <h2 class="stamp-form__heading">
        お疲れ様です！
    </h2>
    <div class="stamp-form__inner">
        <form class="stamp-form__button" action="">
            @csrf
            <input class="stamp-form__button-type1" type="submit" value="勤務開始">
            <input class="stamp-form__button-type2" type="submit" value="勤務終了">
            <input class="stamp-form__button-type1" type="submit" value="休憩開始">
            <input class="stamp-form__button-type2" type="submit" value="休憩開始">
        </form>
    </div>
</div>
@endsection('content')