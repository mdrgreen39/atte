<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('stamp');
    }

    public function show()
    {
        return view('auth.login');
    }


}
