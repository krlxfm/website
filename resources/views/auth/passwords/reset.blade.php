@extends('layouts.login', ['title' => 'Reset Password'])

@section('content')
<h1 class="head-sans-serif"><strong>Let's get you back in there</strong></h1>
<p>Please choose a new password, 12 characters or longer.</p>
<form name="reset-password" method="post" action="{{ route('password.request') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
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
    <button type="submit" class="btn btn-dark btn-lg">Reset password</button>
</form>
@endsection
