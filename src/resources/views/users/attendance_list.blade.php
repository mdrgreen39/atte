@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/users/attendance_list.css') }}">
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



<div class="attendance-list">
    <div class="attendance-list__inner">
        <div class="attendance-list__item">
            <p class="attendance-list__text">{{ $user->name }}の勤怠表</p>
            <form class="attendance-list__search" action="{{ route('attendance_list.filter') }}" method="get">
                @csrf
                <div class="attendance-list__search-input-wrapper">
                    <input class="attendance-list__search-date" type="date" name="start_date">
                    <input class="attendance-list__search-date" type="date" name="end_date">
                    <button class="attendance-list__search-button" type="submit">検索</button>
                </div>


            </form>


        </div>

        @if ($attendances->count() > 0)
        <table class="attendance-list__table">
            <tr class="attendance-list__row">
                <th class="attendance-list__label">日付</th>
                <th class="attendance-list__label">出勤時間</th>
                <th class="attendance-list__label">退勤時間</th>
                <th class="attendance-list__label">休憩時間</th>
                <th class="attendance-list__label">勤務時間</th>
            </tr>
            @foreach($attendances as $attendance)
            <tr class="attendance-list__row">
                <td class="attendance-list__data">
                    @if($attendance->work_date)
                    {{ $attendance->work_date }}
                    @else
                    N/A
                    @endif
                </td>
                <td class="attendance-list__data">
                    @if($attendance->start_work)
                    {{ $attendance->start_work->format('H:i:s')  }}
                    @else
                    N/A
                    @endif
                </td>
                <td class="attendance-list__data">
                    @if($attendance->end_work)
                    {{ $attendance->end_work->format('H:i:s')  }}
                    @else
                    N/A
                    @endif
                </td>
                <td class="attendance-list__data">
                    @if($attendance->total_break)
                    {{ $attendance->total_break->format('H:i:s')  }}
                    @else
                    N/A
                    @endif
                </td>
                <td class="attendance-list__data">
                    @if($attendance->total_work)
                    {{ $attendance->total_work->format('H:i:s')  }}
                    @else
                    N/A
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        {{ $attendances->appends(request()->query())->links('vendor.pagination.custom') }}
        @else
        <p>No attendance records found.</p>
        @endif
    </div>
</div>


@endsection('content')