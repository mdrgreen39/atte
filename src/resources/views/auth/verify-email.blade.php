@extends('layouts.app')

@section('content')
<div>
    <h1>Verify Your Email Address</h1>
    <p>Before proceeding, please check your email for a verification link.</p>
    <p>If you did not receive the email, <a href="{{ route('verification.send') }}">click here to request another</a>.</p>
</div>
@endsection