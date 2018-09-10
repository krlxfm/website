@extends('layouts.missioncontrol', ['title' => 'Welcome aboard!'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>Welcome aboard!</h1>
            <p>Welcome to KRLX! You're just a few steps away from activating your account and applying for a radio show. Please fill in all fields marked with a red asterisk (<span class="text-danger">*</span>), otherwise you are welcome to fill in as much (or as little) of the other fields as you would like.</p>
            <form action="/onboard" method="post">
                <h2>Required fields</h2>
                @csrf
                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-md-2 col-form-label">
                        Full name <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') ?? Auth::user()->name }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="first_name" class="col-sm-3 col-md-2 col-form-label">
                        What name should we call you? <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') ?? Auth::user()->first_name }}" required>
                        <small id="first_name_help" class="form-text text-muted">This is used in emails and other private settings. Most people use their first (given) name.</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone_number" class="col-sm-3 col-md-2 col-form-label">
                        Phone number <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="tel" name="phone_number" class="form-control" id="phone_number" value="{{ old('phone_number') ?? Auth::user()->phone_number }}" required>
                        <small id="phone_number_help" class="form-text text-muted">Please enter your cell phone if you have one, otherwise request a campus landline from the Telecommunications Office.</small>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-3 col-md-2 pt-0">
                            Carleton status <span class="text-danger">*</span>
                        </legend>
                        <div class="col-sm-9 col-md-10">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="status-student"
                                    type="radio"
                                    name="status"
                                    value="student">
                                <label class="form-check-label" for="status-student">
                                    Student
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="status-faculty"
                                    type="radio"
                                    name="status"
                                    value="faculty">
                                <label class="form-check-label" for="status-faculty">
                                    Faculty member
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="status-staff"
                                    type="radio"
                                    name="status"
                                    value="staff">
                                <label class="form-check-label" for="status-staff">
                                    Staff member
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row" id="classyear-row">
                    <label for="phone_number" class="col-sm-3 col-md-2 col-form-label">
                        Class year <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="number" name="year" class="form-control" id="year" value="{{ old('year') ?? Auth::user()->year }}" min="1900" max="{{ date('Y') + 5 }}">
                        <small id="phone_number_help" class="form-text text-muted">Enter your Carleton class year. If you are off-phase due to a medical or other leave, enter your original class year.</small>
                    </div>
                </div>
                <h2>Optional profile information</h2>
                <p>All of the following fields are optional and may be used in promotional materials. Anything you enter here will be public. Markdown formatting is supported.</p>
                <div class="form-group row">
                    <label for="hometown" class="col-sm-3 col-md-2 col-form-label">
                        Hometown
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" name="hometown" class="form-control" id="hometown" value="{{ old('hometown') ?? Auth::user()->hometown }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="major" class="col-sm-3 col-md-2 col-form-label">
                        Major(s)
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" name="major" class="form-control" id="major" value="{{ old('major') ?? Auth::user()->major }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bio" class="col-sm-3 col-md-2 col-form-label">
                        Bio
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <textarea name="bio" class="form-control" id="bio" rows="3">{{ old('bio') ?? Auth::user()->bio }}</textarea>
                        <small id="bio_help" class="form-text text-muted">Please enter bios in third person.</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="favorite_music" class="col-sm-3 col-md-2 col-form-label">
                        Favorite music
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <textarea name="favorite_music" class="form-control" id="favorite_music" rows="3">{{ old('favorite_music') ?? Auth::user()->favorite_music }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="favorite_shows" class="col-sm-3 col-md-2 col-form-label">
                        Favorite KRLX shows (besides yours)
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <textarea name="favorite_shows" class="form-control" id="favorite_shows" rows="3">{{ old('favorite_shows') ?? Auth::user()->favorite_shows }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Save and activate</button>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/onboard.js" defer></script>
@endpush
