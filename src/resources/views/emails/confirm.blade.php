<!DOCTYPE html>
<html>
<head>
    <title>Confirm your email</title>
</head>
<body>
    <h1>Thank you for registering!</h1>
    <p>please confirm your email address by clicking the link below:</p>
    <a href="{{ url('/email/verify' . $user->emil_verification_token) }}">Confirm Email</a>
</body>
</html>