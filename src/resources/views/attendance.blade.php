@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
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

<div class="attendance">
    <div class="attendance-inner">
        <div class="attendance-list">
            <form class="attendance-list__button" action="{{ route('change-date') }}" method="post">
                @csrf
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="action" value="previous">
                <button class="attendance-list__button-before" type="submit">&lt;</button>
            </form>
            <p class="attendance-list__text">{{ $date->format('Y-m-d') }}</p>
            <form class="attendance-list__button" action="{{ route('change-date') }}" method="post">
                @csrf
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <input type="hidden" name="action" value="next">
                <button class="attendance-list__button-after" type="submit">&gt;</button>
            </form>
        </div>

        <table class="attendance__table">
            <tr class="attendance__row">
                <th class="attendance__label">名前</th>
                <th class="attendance__label">勤務開始</th>
                <th class="attendance__label">勤務終了</th>
                <th class="attendance__label">休憩時間</th>
                <th class="attendance__label">勤務時間</th>
            </tr>
            @foreach($attendances as $attendance)
            <tr class="attendance__row">
                <td class="attendance__data">
                    {{ $attendance->user->name }}
                </td>
                <td class="attendance__data">
                    {{ $attendance->start_work->format('H:i:s') }}
                </td>
                <td class="attendance__data">
                    {{ $attendance->end_work->format('H:i:s') }}
                </td>
                <td class="attendance__data">
                    {{ $attendance->total_break->format('H:i:s') }}
                </td>
                <td class="attendance__data">
                    {{ $attendance->total_work->format('H:i:s') }}
                </td>
            </tr>
            @endforeach
        </table>

        {{ $attendances->appends(request()->query())->links('vendor.pagination.custom') }}

    </div>

</div>

@endsection('content')