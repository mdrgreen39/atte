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
            <form class="attendance-list__search" action=" {{ route('users.attendance_list.search') }}" method="get">
                @csrf
                <div class="attendance-list__search-input-wrapper">
                    <div class="attendance-list__search-input-label">
                        <input class="attendance-list__search-input" type="text" name="name" id="name" placeholder="名前" value="{{ request('name') }}">
                    </div>
                    <div class="attendance-list__search-input-label">
                        <input class="attendance-list__search-input" type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="attendance-list__search-input-label">
                        <input class="attendance-list__search-input" type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="attendance-list__search-action">
                        <button class="attendance-list__search-button" type="submit">検索</button>
                        <button class="attendance-list__search-button" type="submit" name="reset" value="true">リセット</button>
                    </div>

                </div>
            </form>

            @php
            $users = $users ?? collect();
            @endphp

            @if($users->isNotEmpty())
            <h2 class="">検索結果</h2>
            <ul class="">
                @foreach($users as $user)
                <li class="">
                    <form class="" action="{{ url('/users/attendance_list') }}" method="get">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <input type="hidden" name="name" value="{{ request('name') }}">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <button class="" type="submit">{{ $user->name }}</button>
                    </form>
                </li>
                @endforeach
            </ul>
            @elseif($selectedUser)
            <h2 class="attendance-list__text">{{ $selectedUser->name }}の勤怠表</h2>
        </div>

        @if ($hasSearchCondition)
        <table class="attendance-list__table">
            <tr class="attendance-list__row">
                <th class="attendance-list__label">日付</th>
                <th class="attendance-list__label">出勤時間</th>
                <th class="attendance-list__label">退勤時間</th>
                <th class="attendance-list__label">休憩時間</th>
                <th class="attendance-list__label">勤務時間</th>
            </tr>
            @foreach($attendanceList as $attendance)
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
        {{ $attendanceList->appends(request()->query())->links('vendor.pagination.custom') }}
        @else
        <div class="attendance-list__status">
            <p class="attendance-list__status-text">No attendance records found.</p>
        </div>
        @endif
        @else
        <div class="attendance-list__status">
            <p class="attendance-list__status-text">No attendance users found.</p>
        </div>
        @endif
    </div>
</div>


@endsection('content')