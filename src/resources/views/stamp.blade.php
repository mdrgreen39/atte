@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp.css') }}">
@endsection

@section('nav')
@can('edit')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('stamp') }}">ホーム</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('attendance') }}">日付一覧</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('users.index') }}">ユーザー一覧</a></li>
        <li class="header-nav__item">
            <a class="header-nav__link" href="{{ route('users.attendance_list', Auth::id()) }}">ユーザー別勤怠一覧</a>
        </li>
        <li class="header-nav__item">
            <form class="header-nav__button" action="/logout" method="post" novalidate>
                @csrf
                <button class="header-nav__button-submit" type="submit">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>

@else

<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('stamp') }}">ホーム</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('attendance') }}">日付一覧</a></li>
        <li class="header-nav__item">
            <form class="header-nav__button" action="/logout" method="post" novalidate>
                @csrf
                <button class="header-nav__button-submit" type="submit">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
@endcan
@endsection

@section('content')
<div class="stamp-form">
    <h2 class="stamp-form__heading">
        {{ $user->name }}さんお疲れ様です！
    </h2>
    <div class="stamp-form__status">
        @if (session('message'))
        <div class="stamp-form__status--success">
            {{ session('message') }}
        </div>
        @endif
        @if (session('error'))
        <div class="stamp-form__status--danger">
            {{ session('error') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="stamp-form__status--danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <div class="stamp-form__form">
        <form class="stamp-form__start-work " action="{{ route('start-work') }}" method="post">
            @csrf
            <button class="stamp-form__button-startwork stamp-form__button" type="submit" name="start_work" id="start_work" value="start_work">
                勤務開始
            </button>
        </form>
        <form class="stamp-form__end-work" action="{{ route('end-work') }}" method="post">
            @csrf
            <button class="stamp-form__button-endwork stamp-form__button" type="submit" name="end_work" id="end_work" value="end_work">
                勤務終了
            </button>
        </form>
        <form class="stamp-form__start-break" action="{{ route('start-break') }}" method="post">
            @csrf
            <button class="stamp-form__button-startbreak stamp-form__button" type="submit" name="start_break" id="start_break" value="start_break">
                休憩開始
            </button>
        </form>
        <form class="stamp-form__end-break" action="{{ route('end-break') }}" method="post">
            @csrf
            <button class="stamp-form__button-endbreak stamp-form__button" type="submit" name="end_break" id="end_break" value="end_break">
                休憩終了
            </button>
        </form>
    </div>
</div>
@endsection('content')