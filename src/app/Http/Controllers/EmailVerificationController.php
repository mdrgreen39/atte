<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Controller;
use App\Models\User;


class EmailVerificationController extends Controller
{
    public function show ()
    {
        return view ('auth.verify-email');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // ハッシュが一致するかどうかを確認
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->withErrors(['email' => 'Invalid verification link.']);
        }

        // ユーザーがすでにメールを確認済みである場合
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'このメールアドレスはすでに確認済みです!ログイン画面からログインしてください');
        }

        // メールアドレスを確認
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // ログアウトしてリダイレクト
        Auth::logout();

        return redirect('/login')->with('status', 'メールアドレスを確認しました!ログイン画面よりログインしてください');
    }

    public function showResendForm()
    {
        return view('auth.resend-verification');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'このメールアドレスはすでに確認済みです!ログイン画面からログインしてください');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->route('login')->with('status', '確認メールを送信しました。メールボックスをご確認ください');
    }
}
