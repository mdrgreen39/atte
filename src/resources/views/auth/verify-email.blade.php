<!DOCTYPE html>

<head>
    <title>Email Verification</title>
</head>

<body>
    <h1>Email Verification</h1>
    <p>Thank you for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn’t receive the email, we will gladly send you another.</p>

    <!--@if (session('status') == 'verification-link-sent')
    <p>A new verification link has been sent to the email address you provided during registration.</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">Resend Verification Email</button>
    </form>-->

    <p><a href="{{ route('login') }}">Return to login screen</a></p>
</body>

</html>