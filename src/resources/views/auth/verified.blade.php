<!DOCTYPE html>
<html>

<head>
    <title>Email Verified</title>
</head>

<body>
    <h1>Thank you for verifying your email, {{ $user->name }}!</h1>
    <p>Your email has been successfully verified.</p>
    <p>Click the link below to login.</p>
    <a href="{{ route('login') }}">Login</a>
</body>

</html>