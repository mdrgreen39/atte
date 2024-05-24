<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/auth/login', [AttendanceController::class, 'show']);

Route::middleware('auth')->group(function ()
{
    Route::get('/', [AttendanceController::class, 'index'])->name('stamp');
    Route::post('/start-work', [AttendanceController::class, 'startWork'])->name('start-work');
    Route::post('/end-work', [AttendanceController::class, 'endWork'])->name('end-work');
    Route::post('/start-break', [AttendanceController::class, 'startBreak'])->name('start-break');
    Route::post('/end-break', [AttendanceController::class, 'endBreak'])->name('end-break');




    Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/change-date', [AttendanceController::class, 'changeDate'])->name('change-date');

});