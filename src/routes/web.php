<?php

use App\Events\EmailVerified;
use App\Mail\MailTest;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;
use App\Models\Attendance;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::get('/auth/login', [AttendanceController::class, 'showLoginForm'])->name('login');

Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});

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

Route::group(['middleware' => ['permission:edit']], function() {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/attendance_list', [UserController::class, 'attendanceList'])->name('users.attendance_list');
    Route::get('/users/attendance_list', [UserController::class, 'searchAttendanceList'])->name('users.attendance_list.search');
    //Route::get('/users/filter', [UserController::class, 'filter'])->name('attendance_list.filter');
});



Route::get('/email/verify', [EmailVerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/mail', function () {
    $mail_text = "テストです";
    Mail::to('to_address@example.com')->send(new MailTest($mail_text));
});
