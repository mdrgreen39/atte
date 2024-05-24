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
        $today = CarbonImmutable::today();
        $attendance = Attendance::where('user_id',$user->id)->where('work_date', $today)->first();

        if (!$user){
            return redirect()->route('login');
        }

        return view('stamp', compact('user', 'attendance', 'today'));
    }

    /* ログインページ表示 */
    public function show()
    {
        return view('auth.login');
    }

    /* 出勤登録 */
    public function startWork()
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();
        $now = CarbonImmutable::now();

        $exitingAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('end_work')
            ->first();

        if($exitingAttendance) {
            return redirect()->back()->with('error', '勤務終了時間が登録がされていません');
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today,
            'start_work' => now()
        ]);

        return redirect()->back()->with('message', '勤務開始時間が登録されました');
    }

    /* 退勤時間登録 */
    public function endWork (Request $request)
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();
        $now = CarbonImmutable::now();


        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', '<=', $today)
            ->whereNull('end_work')
            ->first();

dd($attendance, $today);
        if($attendance) {

            // start_workの値をデバッグ出力
            $startWork = CarbonImmutable::parse($attendance->start_work);
            $endWork = $now;
            dd($startWork->toDateTimeString(), $endWork->toDateTimeString());


            // 未終了の休憩があるか確認
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

                if ($unfinishedBreak) {
                    return redirect()-> back()->with('error', '休憩終了時間が登録されていません');
                }

            // 出勤から退勤までの時間を計算
            $startWork = CarbonImmutable::parse($attendance->start_work);
            $endWork = $now;

            dd($startWork->toDateTimeString(), $endWork->toDateTimeString());

            // 日付を跨いでいる場合、当日と翌日に分ける
            if ($endWork->isNextDay($startWork)) {
                // 現在の勤務記録を終了時間をその日の23:59:59に設定
                $attendance->end_work = $startWork->copy()->endOfDay();
                $totalWorkDuration = $startWork->diffInSeconds($attendance->end_work);

            // 休憩時間の合計を計算
            $breaks = $attendance->breakTimes;
            $totalBreakDuration = 0;
            if ($breaks->isNotEmpty()) {
                foreach ($breaks as $break) {
                    $startBreak = CarbonImmutable::parse($break->start_break);
                    $endBreak = CarbonImmutable::parse($break->end_break);
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

            // 新しい出勤記録を作成
            Attendance::create([
                'user_id' => $user->id,
                'work_date' => $endWork->toDateString(),
                'start_work' =>$endWork->copy()->startOfDay(),
                'end_work' => $endWork,
                'total_break' => '00:00:00',
            ]);

            return redirect()->back()->with('message', '勤務終了時間が登録されました');

        }

            // 日付を跨いでいない場合の処理
            $attendance->end_work = $endWork;
            $totalWorkDuration = $startWork->diffInSeconds($endWork);

            // 休憩時間の合計を計算
            $breaks = $attendance->breakTimes;
            $totalBreakDuration = 0;
            if ($breaks->isNotEmpty()) {
                foreach ($breaks as $break) {
                    $startBreak = CarbonImmutable::parse($break->start_break);
                    $endBreak = CarbonImmutable::parse($break->end_break);
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

        return redirect()->back()->with('error','勤務開始時間が登録されていません');
    }

    /* 休憩時間開始登録 */
    public function startBreak()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $attendance = Attendance::where('user_id', $user->id)
            ->where(function($query) use ($today, $yesterday) {
                $query->where('work_date', $today)
                    ->orWhere('work_date', $yesterday);
            })
            ->whereNull('end_work')
            ->first();

        if ($attendance) {
            // 未終了の休憩が存在するか確認
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

            if ($unfinishedBreak) {
                return redirect()->back()->with('error', '休憩終了時間が登録されていません');
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
            ->where(function($query) use ($today, $yesterday) {
                $query->where('work_date', $today)
                    ->orWhere('work_date', $yesterday);
            })
            ->whereNull('end_work')
            ->first();

        if($attendance) {
            // 未終了の休憩を習得
            $unfinishedBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_break')
                ->first();

            if($unfinishedBreak){
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
        $date = $request->session()->get('date',Carbon::yesterday());

        $attendances = Attendance::whereDate('work_date', $date)->paginate(5);

        return view('attendance', compact('user', 'attendances', 'date'));

    }

    public function changeDate(Request $request)
    {
        $date = Carbon::parse($request->input('date'));
        $action = $request->input('action');

        if($action === 'previous') {
            $date->subDay();
        }elseif($action === 'next') {
            $date->addDay();
        }

        $request->session()->put('date', $date);

        return redirect()->route('attendance');
    }


}
