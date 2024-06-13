<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(('permission:edit'));
    }

    public function index(Request $request)
    {
        $this->authorize('edit');

        if ($request->has('reset')) {
            return redirect('/users')->withInput();
        }

        $query = User::query();


        if ($request->filled('name')) {
            $name = $request->name;
            $names = preg_replace('/\s+/', '', $name);
            $query->where(DB::raw('REPLACE(name, " ", "")'), 'like', '%' . $names . '%');
        }


        //$keyword = $request->input('keyword');
        //if ($keyword) {
        //    $query->where('id', $keyword)
        //        ->orWhere('name', 'like', "%{$keyword}%");
        //}

        $users = $query->paginate(5);
        return view('users.index', compact('users'));
    }

    public function attendanceList()
    {
        $this->authorize('edit');

        //$user = User::findOrFail($id);
        //$attendances = $user->attendances()->paginate(5);
        return view('users.attendance_list', ['attendanceList' => collect(), 'selectedUser' => null, 'hasSearchCondition' => false]);
    }

    public function searchAttendanceList(Request $request)
    {
        $this->authorize('edit');

        if ($request->has('reset')) {
            return redirect('/users/attendance_list');
        }

        $hasSearchCondition = $request->filled('id') || $request->filled('name') || $request->filled('start_date') || $request->filled('end_date');


        //$users = User::query()->get();

        $users = collect();
        $selectedUser = null;
        $attendanceList = collect();

        if($hasSearchCondition) {
            $query = User::query();

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            if ($request->filled('name')) {
                $name = $request->name;
                $names = preg_replace('/\s|　+/', '', $name);
                $query->where(DB:: raw('REPLACE(REPLACE(name, " ", ""), "　", "")'), 'like', '%' . $names . '%');
            }

            if ($request->filled('id')) {
                $selectedUser = $query->first();
                if ($selectedUser) {
                    $userAttendance = $selectedUser->attendances();

                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $userAttendance->whereBetween('work_date', [$request->start_date, $request->end_date]);
                    } elseif ($request->filled('start_date')) {
                        $userAttendance->where('work_date', '>=', $request->start_date);
                    } elseif ($request->filled('end_date')) {
                        $userAttendance->where('work_date', '<=', $request->end_date);
                    }
                    $attendanceList = $userAttendance->paginate(5);

                    return view('users.attendance_list', compact('users', 'attendanceList', 'selectedUser', 'hasSearchCondition'));
                }
            }

            $users = $query->paginate(5);

            if ($users->count() === 1) {
                $selectedUser = $users->first();$userAttendance = $selectedUser->attendances();

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $userAttendance->whereBetween('work_date', [$request->start_date, $request->end_date]);
                } elseif ($request->filled('start_date')) {
                    $userAttendance->where('work_date', '>=', $request->start_date);
                } elseif ($request->filled('end_date')) {
                    $userAttendance->where('work_date', '<=', $request->end_date);
                }

                $attendanceList = $userAttendance->paginate(5);
            }
        }

        return view('users.attendance_list', compact('users', 'attendanceList', 'selectedUser', 'hasSearchCondition'));

    }



    //public function filter(Request $request, int $id = null)
    //{
    //    $this->authorize('edit');

    //    $loggedInUser = Auth::user();

    //    if (is_null($id)) {
    //        $id = $loggedInUser->id;
    //    }

    //    $user = User::findOrFail($id);
    //    $startDate = $request->input('start_date');
    //    $endDate = $request->input('end_date');

    //    $query = Attendance::where('user_id', $user->id);

    //    if ($startDate) {
    //        $query->where('work_date', '>=', $startDate);
    //    }

    //    if ($endDate) {
    //        $query->where('work_date', '<=', $endDate);
    //    }

    //    $attendances = $query->paginate(5);

    //    return view('users.attendance_list', compact('user', 'attendances'));

    //}

    //public function attendanceList(Request $request, int $id = null)
    //{
    //    $loggedInUser = Auth::user();

    //    if (is_null($id)) {
    //        $id = $loggedInUser->id;
    //    }

    //    $user = User::findOrFail($id);


    //    $attendancesQuery = Attendance::where('user_id', '!=', $user->id);


    //    $keyword = $request->input('keyword');
    //    if ($keyword) {
    //        $attendancesQuery->where(function($query) use ($keyword) {
    //            $query->where('id', $keyword)
    //                ->orWhereHas('user', function($query) use ($keyword) {
    //                $query->where('name', 'like', "%{$keyword}%");
    //            });
    //        });
    //    }

        //dd($attendancesQuery->toSql(), $attendancesQuery->getBindings());

    //   $attendances = $attendancesQuery->paginate(5);

    //   return view('users.attendance_list', compact('user', 'attendances'));
    //}




}
