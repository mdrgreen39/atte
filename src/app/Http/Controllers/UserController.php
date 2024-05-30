<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $keyword = $request->input('keyword');
        if ($keyword) {
            $query->where('id', $keyword)
                ->orWhere('name', 'like', "%{$keyword}%");
        }

        $users = $query->paginate(5);
        return view('users.index', compact('users'));
    }

    public function attendanceList($id)
    {
        $user = User::findOrFail($id);
        $attendances = $user->attendances()->paginate(5);
        return view('users.attendance_list', compact('user', 'attendances'));
    }

    public function filter(Request $request, int $id = null)
    {
        $loggedInUser = Auth::user();

        if (is_null($id)) {
            $id = $loggedInUser->id;
        }

        $user = User::findOrFail($id);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Attendance::where('user_id', $user->id);

        if ($startDate) {
            $query->where('work_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('work_date', '<=', $endDate);
        }

        $attendances = $query->paginate(5);

        return view('users.attendance_list', compact('user', 'attendances'));



    }

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
