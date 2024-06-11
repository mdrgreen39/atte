<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Controller;
use App\Events\EmailVerified;
use App\Models\User;


class EmailVerificationController extends Controller
{
    //public function show ()
    //{
    //    return view ('auth.verify-email');
    //}

    //public function verify(EmailVerificationRequest $request)
    //{
    //    $request->fulfill();
    //    event(new Verified($request->user()));

    //    return redirect('/dashboard')->with('verified', true);
    //}

    //public function resend(Request $request)
    //{
    //    $request->user()->sendEmailVerificationNotification();

     //   return back()->with('message', 'Verification link sent!');
    //}

    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->firstOrFail();
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return redirect('/login')->with('message', 'Email verified!');
    }


}
