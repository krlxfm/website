@extends('layouts.missioncontrol', ['title' => 'Logistics - Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <a href="{{ route('board.app', $app->year) }}"><i class="fas fa-chevron-left mr-1"></i>{{ $app->year . ' - ' . ($app->year + 1) }} BOARD APPLICATION</a>
            <h1 class="mb-3 mt-2">Logistics</h1>
            <form method="post">
                @method('patch')
                @csrf
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-3 pt-0">
                            Are you currently on campus?
                        </legend>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-no"
                                    type="radio"
                                    name="remote"
                                    value="0"
                                    {{ $app->remote ? '' : 'checked' }}>
                                <label class="form-check-label" for="remote-no">
                                    Yes - I am currently on campus and can attend my interview in person
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-yes"
                                    type="radio"
                                    name="remote"
                                    value="1"
                                    {{ $app->remote ? 'checked' : '' }}>
                                <label class="form-check-label" for="remote-yes">
                                    No - I am currently off campus and will need to complete my interview via video conference
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-3 pt-0">
                            Preferred video platform
                            <br>
                            <a href="{{ 'mailto:it@' . env('MAIL_DOMAIN', 'example.org') }}">(don't have any of these?)</a>
                        </legend>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="platform-facebook"
                                    type="radio"
                                    name="remote_platform"
                                    value="facebook"
                                    {{ $app->remote_platform == 'facebook' ? 'checked' : '' }}>
                                <label class="form-check-label" for="platform-facebook">
                                    <i class="fab fa-facebook-messenger fa-fw"></i> Facebook Messenger
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="platform-skype"
                                    type="radio"
                                    name="remote_platform"
                                    value="skype"
                                    {{ $app->remote_platform == 'skype' ? 'checked' : '' }}>
                                <label class="form-check-label" for="platform-skype">
                                    <i class="fab fa-skype fa-fw"></i> Skype
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="platform-hangouts"
                                    type="radio"
                                    name="remote_platform"
                                    value="hangouts"
                                    {{ $app->remote_platform == 'hangouts' ? 'checked' : '' }}>
                                <label class="form-check-label" for="platform-hangouts">
                                    <i class="fas fa-video fa-fw"></i> Google Hangouts
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <label for="remote_contact" class="col-sm-3 col-form-label">
                        Profile information
                    </label>
                    <div class="col-sm-9">
                        <input type="text" name="remote_contact" class="form-control" id="remote_contact" value="{{ old('remote_contact') ?? $app->remote_contact }}">
                        <small id="remote_contact_help" class="form-text text-muted">
                            <strong>Facebook:</strong> enter your full Facebook profile URL from <a href="https://www.facebook.com/settings" target="_blank">Facebook Settings &gt; General</a> (should start with facebook.com).<br>
                            <strong>Skype:</strong> enter the email address listed on <a href="https://secure.skype.com/portal/profile" target="_blank">your profile</a>.<br>
                            <strong>Google Hangouts:</strong> enter your Carleton email address.
                        </small>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-3 pt-0">
                            Will you be abroad between Spring {{ $app->year }} and Winter {{ $app->year + 1 }}?
                        </legend>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-none"
                                    type="radio"
                                    name="ocs"
                                    value="none"
                                    {{ $app->ocs == 'none' ? 'checked' : '' }}>
                                <label class="form-check-label" for="abroad-none">
                                    No - I am planning to be on campus for Spring {{ $app->year }}, Fall {{ $app->year }}, and Winter {{ $app->year + 1 }}.
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-spring"
                                    type="radio"
                                    name="ocs"
                                    value="abroad_sp"
                                    {{ $app->ocs == 'abroad_sp' ? 'checked' : '' }}>
                                <label class="form-check-label" for="abroad-spring">
                                    Yes - I will be abroad Spring {{ $app->year }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-fall"
                                    type="radio"
                                    name="ocs"
                                    value="abroad_fa"
                                    {{ $app->ocs == 'abroad_fa' ? 'checked' : '' }}>
                                <label class="form-check-label" for="abroad-fall">
                                    Yes - I will be abroad Fall {{ $app->year }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    id="abroad-winter"
                                    type="radio"
                                    name="ocs"
                                    value="abroad_wi"
                                    {{ $app->ocs == 'abroad_wi' ? 'checked' : '' }}>
                                <label class="form-check-label" for="abroad-winter">
                                    Yes - I will be abroad Winter {{ $app->year + 1 }}
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <h3>Interview time preferences</h3>
                <p>You will be assigned an interview time based on the position(s) you apply for, as well as your availability as indicated below. Please be as flexible as you can - we'll try to let you know of your time as quickly as we can after applications close. <strong>You must provide an answer in each row.</strong></p>
                <div class="alert alert-info">
                    If none of these times work, please <a class="alert-link" href="{{ 'mailto:manager@'.env('MAIL_DOMAIN', 'example.org') }}">email the Station Manager</a> to schedule an alternative time. All times are listed in US/Central, and slots are 15 minutes long.
                </div>
                <table class="table table-responsive-sm">
                    <thead>
                        <tr class="text-center">
                            <th>Time</th>
                            <th style="background: #ffbbbb">Unavailable</th>
                            <th style="background: #ffffbb">If&nbsp;need&nbsp;be</th>
                            <th style="background: #bbffbb">Available</th>
                            <th style="background: #bbbbff">Preferred</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dates as $date)
                            <tr>
                                <td>
                                    {!! str_replace(' ', '&nbsp;', $date->format('D, M j,')) !!}
                                    {!! str_replace(' ', '&nbsp', $date->format('g:i a')) !!}
                                </td>
                                <td class="text-center" style="background: #ffdddd">
                                    <input
                                        class="form-check-input"
                                        id="{{ $date->format('Y-m-d_H:i') }}-1"
                                        type="radio"
                                        name="interview_schedule[{{ $date->format('Y-m-d H:i:s') }}]"
                                        value="1"
                                        {{ $app->interview_schedule[$date->format('Y-m-d H:i:s')] == 1 ? 'checked' : '' }}>
                                </td>
                                <td class="text-center" style="background: #ffffdd">
                                    <input
                                        class="form-check-input"
                                        id="{{ $date->format('Y-m-d_H:i') }}-2"
                                        type="radio"
                                        name="interview_schedule[{{ $date->format('Y-m-d H:i:s') }}]"
                                        value="2"
                                        {{ $app->interview_schedule[$date->format('Y-m-d H:i:s')] == 2 ? 'checked' : '' }}>
                                </td>
                                <td class="text-center" style="background: #dfffdf">
                                    <input
                                        class="form-check-input"
                                        id="{{ $date->format('Y-m-d_H:i') }}-3"
                                        type="radio"
                                        name="interview_schedule[{{ $date->format('Y-m-d H:i:s') }}]"
                                        value="3"
                                        {{ $app->interview_schedule[$date->format('Y-m-d H:i:s')] == 3 ? 'checked' : '' }}>
                                </td>
                                <td class="text-center" style="background: #ddddff">
                                    <input
                                        class="form-check-input"
                                        id="{{ $date->format('Y-m-d_H:i') }}-4"
                                        type="radio"
                                        name="interview_schedule[{{ $date->format('Y-m-d H:i:s') }}]"
                                        value="4"
                                        {{ $app->interview_schedule[$date->format('Y-m-d H:i:s')] == 4 ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Save and continue</button>
                </p>
            </form>
        </div>
    </div>
@endsection
