@extends('layouts.login', ['title' => 'Register'])

@section('content')
<h1 class="head-sans-serif"><strong>Welcome aboard</strong></h1>
<p>Please fill out all information listed here to register your account.</p>
<p>
    <a href="{{ route('login') }}">Already have an account?</a> | <a href="{{ route('login') }}">Want to use a different email?</a>
</p>
<form name="register" method="post" action="{{ route('register') }}">
    @csrf
    <div class="form-group row">
        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label" for="email">Email address</label>
        <div class="col-sm-6 col-md-8 col-lg-9">
            <input type="email" readonly class="form-control-plaintext" id="email" name="email" dusk="email" value="{{ session('email') ?? old('email') }}" placeholder="luke.skywalker@rebelalliance.net" required autocomplete="email">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label" for="email">Full name</label>
        <div class="col-sm-6 col-md-8 col-lg-9">
            <input type="text" class="form-control" id="name" name="name" dusk="name" value="{{ old('name') }}" placeholder="Luke Skywalker" required autofocus autocomplete="name">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label" for="password">Password</label>
        <div class="col-sm-6 col-md-8 col-lg-9">
            <input type="password" class="form-control" id="password" name="password" dusk="password" placeholder="password" minlength="12" autocomplete="new-password" required>
            <small id="passwordHelp" class="form-text text-muted">Passwords must be 12 characters or longer, but can have any combination of characters in them.</small>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label" for="password">Password, again</label>
        <div class="col-sm-6 col-md-8 col-lg-9">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" dusk="password_confirmation" placeholder="password, again" required>
        </div>
    </div>
    <button type="submit" class="btn btn-dark btn-lg">Create account</button>
</form>
@endsection
