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
    public function show ()
    {
        return view ('auth.verify-email');
    }

    //トークン使ったメール認証
    //public function verify($token)
    //{
    //    $user = User::where('email_verification_token', $token)->firstOrFail();
    //   $user->email_verified_at = now();
    //    $user->email_verification_token = null;
    //    $user->save();

    //    return view('emails.verified',['user'=>$user]);

        //return redirect('/login')->with('message', 'Email verified!');
    //}

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        event(new Verified($request->user()));

        return redirect('/login')->with('message', 'Email verified!');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('login');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }

    


}
