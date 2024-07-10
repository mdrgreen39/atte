<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        $users = $query->paginate(5);
        return view('users.index', compact('users'));
    }

    public function attendanceList()
    {
        $this->authorize('edit');

        return view('users.attendance_list', ['attendanceList' => collect(), 'selectedUser' => null, 'hasSearchCondition' => false]);
    }

    public function searchAttendanceList(Request $request)
    {
        $this->authorize('edit');

        if ($request->has('reset')) {
            return redirect('/users/attendance_list');
        }

        $hasSearchCondition = $request->filled('id') || $request->filled('name') || $request->filled('start_date') || $request->filled('end_date');

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

            //if ($request->filled('id') && $request->filled('name')) {
                $users = $query->paginate(5);
            //} else {
                $users = $query->paginate(5);
            //}

            if ($users->count() === 1) {
                $selectedUser = $users->first();
                $userAttendance = $selectedUser->attendances();

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
}
