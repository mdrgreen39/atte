@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/users/index.css') }}">
@endsection

@section('nav')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('stamp') }}">ホーム</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('attendance') }}">日付一覧</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('users.index') }}">ユーザー一覧</a></li>
        <li class="header-nav__item"><a class="header-nav__link" href="{{ route('users.attendance_list', Auth::id()) }}">ユーザー勤怠一覧</a></li>
        <li class="header-nav__item">
            <form class="header-nav__button" action="/logout" method="post" novalidate>
                @csrf
                <button class="header-nav__button-submit" type="submit">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
@endsection

@section('content')

<div class="user">
    <div class="user-inner">
        <div class="user-list">

            <form class="user-list__search" action=" {{ route('users.index') }}" method="get">
                @csrf
                <div class="user-list__search-input-wrapper">
                    <input class="user-list__search-input" type="text" name="name" placeholder="名前" value="{{ request('name') }}">
                    <button class="user-list__search-button" type="submit">検索</button>
                    <button class="user-list__search-button" type="submit" name="reset" value="true">リセット</button>
                </div>
            </form>
            <h2 class="user-list__text">ユーザー一覧</h2>
        </div>
        @if($users->isNotEmpty())
        <table class="user__table">
            <tr class="user__row">
                <th class="user__label">ユーザーID</th>
                <th class="user__label">名前</th>
                <th class="attendance__label">メールアドレス</th>
            </tr>

            @foreach($users as $user)
            <tr class="user__row">
                <td class="user__data">
                    {{ $user->id }}
                </td>
                <td class="user__data">
                    {{ $user->name }}
                </td>
                <td class="user__data">
                    {{ $user->email }}
                </td>
            </tr>
            @endforeach
        </table>

        {{ $users->appends(request()->query())->links('vendor.pagination.custom') }}

        @else
        <div class="attendance-list__status">
            <p class="attendance-list__status-text">No users found.</p>
        </div>
        @endif

    </div>
</div>


@endsection('content')