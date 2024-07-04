<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthenticatedController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

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
    Route::get('/login', [AuthenticatedController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthenticatedController::class, 'store'])
        ->middleware('guest');

    Route::post('/logout', [AuthenticatedController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6.1'])
    ->name('verification.verify');

Route::get('email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');

Route::get('/email/resend', [EmailVerificationController::class, 'showResendForm'])->name('verification.resend');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend.post');

Route::middleware('auth')->group(function ()
{
    Route::get('/', [AttendanceController::class, 'index'])
        ->middleware('verified')
        ->name('stamp');
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
});

//メールテスト用
Route::get('/test-email', function () {
    if (App::environment('production') && !config('mail.test_mode')) {

        return 'Test email is disabled in production';
    }

    Mail::raw('This is a test email', function ($message) {
        //to('')に送信先のメールアドレスを入力：例('xxx@example.com')
        $message->to('imakoko39@gmail.com')
        ->subject('Test Email');
    });

    return 'Test email sent!';
});


