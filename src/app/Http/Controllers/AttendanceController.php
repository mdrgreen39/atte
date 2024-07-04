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
    private function formatTimeFromSeconds($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }


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

        $exitingAttendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now(),
            'start_work' => now(),
        ]);

        return redirect()->back()->with('message', '勤務開始時間が登録されました');
    }

    /* 退勤時間登録 */
    public function endWork(Request $request)
    {
        $user = Auth::user();
        $endWork = now();

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 現在の出勤記録を取得
        $currentAttendance = Attendance::where('user_id', $user->id)
        ->where(function ($query) use ($today, $yesterday) {
            $query->whereDate('start_work', $today)
                ->orWhereDate('start_work', $yesterday);
        })
        ->whereNull('end_work')
        ->first();

        if (!$currentAttendance) {
            return redirect()->back()->with('error', '勤務開始時間が登録されていません');
        }

        // 未終了の休憩があるか確認
        $unfinishedBreak = BreakTime::where('attendance_id', $currentAttendance->id)
        ->whereNull('end_break')
        ->first();

        if ($unfinishedBreak) {
            return redirect()->back()->with('error', '休憩終了時間が登録されていません');
        }

        //前日の未終了の勤務を確認
        $previousAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('start_work', $yesterday)
            ->whereNull('end_work')
            ->first();

        //前日の勤務レコードを作成
        if ($previousAttendance) {

            //前日の勤務終了時間を23:59:59に設定
            $previousAttendance->end_work = $yesterday->endOfDay()->toDateTimeString();

            //出勤から退勤までの時間を計算
            $startWork = Carbon::parse($previousAttendance->start_work);
            $totalWorkDuration = $startWork->diffInSeconds($yesterday->endOfDay());

            //前日の休憩時間の合計を計算
            $breaks = BreakTime::where('attendance_id', $previousAttendance->id)
                ->whereDate('start_break', $yesterday)
                ->get();
            $totalBreakDuration = 0;
            if ($breaks->isNotEmpty()) {
                foreach ($breaks as $break) {
                    $startBreak = Carbon::parse($break->start_break);
                    $endBreak = Carbon::parse($break->end_break ?? $yesterday->endOfDay());
                    $totalBreakDuration += $startBreak->diffInSeconds($endBreak);
                }
            }

            //休憩を引いた総勤務時間を計算
            $totalWorkDuration -= $totalBreakDuration;

            //総勤務時間と休憩時間を保存
            $previousAttendance->total_break = $this->formatTimeFromSeconds($totalBreakDuration);
            $previousAttendance->total_work = $this->formatTimeFromSeconds($totalWorkDuration);
            $previousAttendance->save();

            //日付を跨いでいる場合、新しい出勤記録を作成

            $newAttendance = new Attendance();
            $newAttendance->user_id = $user->id;
            $newAttendance->start_work = $today->startOfDay()->toDateTimeLocalString();
            $newAttendance->work_date = $today->toDateString();
            $newAttendance->end_work = $endWork;

            $startWork = $today->startOfDay();
            $totalWorkDuration = $startWork->diffInSeconds($endWork);

            //当日の休憩時間の合計を計算
            $breaks = BreakTime::where('attendance_id', $currentAttendance->id)
                ->whereDate('start_break', $today)
                ->get();
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

            $newAttendance->total_break = $this->formatTimeFromSeconds($totalBreakDuration);
            $newAttendance->total_work = $this->formatTimeFromSeconds($totalWorkDuration);
            $newAttendance->save();

            return redirect()->back()->with('message', '勤務終了時間が登録されました');

        }

        // 日付を跨いでいない場合、通常の処理
        $startWork = Carbon::parse($currentAttendance->start_work);
        $currentAttendance->work_date = $endWork->toDateString();
        $currentAttendance->end_work = $endWork->toDateTimeString();
        $totalWorkDuration = $startWork->diffInSeconds($endWork);

        // 休憩時間の合計を計算
        $breaks = $currentAttendance->breakTimes;
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
        //$totalWork = $today->format('H:i:s', $totalWorkDuration);
        //$totalBreak = $today->format('H:i:s', $totalBreakDuration);

        $currentAttendance->total_break = $this->formatTimeFromSeconds($totalBreakDuration);
        $currentAttendance->total_work = $this->formatTimeFromSeconds($totalWorkDuration);
        $currentAttendance->save();

        return redirect()->back()->with('message', '勤務終了時間が登録されました');
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
