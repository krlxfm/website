@extends('layouts.login', ['title' => 'Sign in'])

@section('content')
<h1 class="head-sans-serif"><strong>Welcome back, {{ session('user')->first_name }}</strong></h1>
<p>Enter your password to finish signing in.</p>
<form name="auth-email" method="post" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <input name="email" type="hidden" class="form-control" value="{{ session('user')->email }}">
    </div>
    <div class="form-group">
        <input name="password" type="password" class="form-control" dusk="login-password" placeholder="password" autofocus required>
    </div>
    <p><a href="{{ route('login') }}">Not {{ session('user')->first_name }}?</a><br><a href="{{ route('password.request') }}">Forgot your password?</a></p>
    <button type="submit" class="btn btn-dark btn-lg">Sign in</button>
</form>
@endsection
