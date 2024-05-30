<?php

namespace App\Http\Controllers;


use App\Http\Requests\StampRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\BreakTime;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{


    /* 打刻ページ表示 */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)->where('work_date', $today)->first();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('stamp', compact('user', 'attendance', 'today'));
    }

    /* ログインページ表示 */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /* 出勤登録 */
    public function startWork()
    {
        $user = Auth::user();

        $exitingAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('end_work')
            ->first();

        if ($exitingAttendance) {
            return redirect()->back()->with('error', '勤務開始時間は登録済みです');
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'start_work' => now()
        ]);

        return redirect()->back()->with('message', '勤務開始時間が登録されました');
    }

    /* 退勤時間登録 */
    public function endWork(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $attendance = Attendance::where('user_id', $user->id)
            ->where(function ($query) use ($today, $yesterday) {
                $query->whereDate('start_work', $today)
                ->orWhereDate('start_work', $yesterday);
            })
            ->whereNull('end_work')
            ->first();

        if ($attendance) {
            // 未終了の休憩があるか確認
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

            if ($unfinishedBreak) {
                return redirect()->back()->with('error', '休憩終了時間が登録されていません');
            }

            // 出勤から退勤までの時間を計算
            $startWork = Carbon::parse($attendance->start_work);
            $endWork = now();

            // 勤務日を退勤日の日付に設定
            $attendance->work_date = $endWork->toDateString();
            $attendance->end_work = $endWork->toDateTimeString();
            $totalWorkDuration = $startWork->diffInSeconds($endWork);

            // 休憩時間の合計を計算
            $breaks = $attendance->breakTimes;
            $totalBreakDuration = 0;
            if ($breaks->isNotEmpty()) {
                foreach ($breaks as $break) {
                    $startBreak = Carbon::parse($break->start_break);
                    $endBreak = Carbon::parse($break->end_break);
                    $totalBreakDuration += $startBreak->diffInSeconds($endBreak);
                }
            }

            // 休憩時間を引いた総勤務時間を計算
            $totalWorkDuration -= $totalBreakDuration;

            // 総勤務時間と休憩時間を保存
            $totalWork = gmdate('H:i:s', $totalWorkDuration);
            $totalBreak = gmdate('H:i:s', $totalBreakDuration);

            $attendance->total_break = $totalBreak;
            $attendance->total_work = $totalWork;
            $attendance->save();

            return redirect()->back()->with('message', '勤務終了時間が登録されました');
        }

        return redirect()->back()->with('error', '勤務開始時間が登録されていません');
    }

    /* 休憩時間開始登録 */
    public function startBreak()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $attendance = Attendance::where('user_id', $user->id)
            ->where(function ($query) use ($today, $yesterday) {
                $query->whereDate('start_work', $today)
                    ->orWhereDate('start_work', $yesterday);
            })
            ->whereNull('end_work')
            ->first();

        if ($attendance) {
            // 未終了の休憩が存在するか確認
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

            if ($unfinishedBreak) {
                return redirect()->back()->with('error', '休憩開始時間は登録済みです');
            }

            // 休憩開始を登録
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_break' => now()
            ]);

            return redirect()->back()->with('message', '休憩時間を登録しました');
        }

        return redirect()->back()->with('error', '勤務開始時間が登録されていません');
    }

    /* 休憩終了登録 */
    public function endBreak(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $attendance = Attendance::where('user_id', $user->id)
            ->where(function ($query) use ($today, $yesterday) {
                $query->whereDate('start_work', $today)
                    ->orWhereDate('start_work', $yesterday);
            })
            ->whereNull('end_work')
            ->first();

        if ($attendance) {
            // 未終了の休憩を習得
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

            if ($unfinishedBreak) {
                // 休憩終了を登録
                $unfinishedBreak->end_break = now();
                $unfinishedBreak->save();

                return redirect()->back()->with('message', '休憩終了時間を登録しました');
            }

            return redirect()->back()->with('error', '休憩開始時間が登録されていません');
        }

        return redirect()->back()->with('error', '勤務開始時間が登録されていません');
    }

    /* 日付別勤怠一覧ページ表示 */
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $date = $request->session()->get('date', Carbon::yesterday());

        $attendances = Attendance::with('user')
            ->whereDate('work_date', $date)
            ->paginate(5);

        return view('attendance', compact('user', 'attendances', 'date'));
    }

    public function changeDate(Request $request)
    {
        $date = Carbon::parse($request->input('date'));
        $action = $request->input('action');

        if ($action === 'previous') {
            $date->subDay();
        } elseif ($action === 'next') {
            $date->addDay();
        }

        $request->session()->put('date', $date);

        return redirect()->route('attendance');
    }
}
