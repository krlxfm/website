@extends('layouts.login', ['title' => 'Sign in or register'])

@section('content')
<h1 class="head-sans-serif"><strong>Welcome</strong></h1>
<p>Enter your email address to create your account or sign in. Current Carleton students, faculty, and staff, please enter your Carleton email address.</p>
<form name="auth-email" method="post" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <input name="email" type="email" class="form-control" dusk="login-email" placeholder="luke.skywalker@rebelalliance.net" autofocus required>
    </div>
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="login-terms" name="terms" dusk="login-terms" value="1">
        <label class="form-check-label" for="login-terms">I have read and agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>, and I grant KRLX permission to use my personal information as described in the Privacy Policy.</label>
    </div>
    <button type="submit" class="btn btn-dark btn-lg">Continue</button>
</form>
@endsection
