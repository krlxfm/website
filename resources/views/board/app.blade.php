@extends('layouts.missioncontrol', ['title' => 'Board Application - '.$app->year])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">{{ $app->year }} &ndash; {{ $app->year + 1 }} Board Application</h1>
            <p><strong>All fields in all sections are mandatory.</strong> Once all sections have been completed (completed sections are marked with <i class="far fa-check-circle"></i> in the sidebar), the submit button will unlock.</p>
        </div>
    </div>
@endsection

@section('mid')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                        @include('board.panelicon', ['complete' => $missing_fields->count() == 0])
                        Profile
                    </a>
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                        @include('board.panelicon', ['complete' => !$logistics_needed])
                        Logistics
                    </a>
                    <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Common</a>
                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Position</a>
                </div>
            </div>
            <div class="col-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <h2>Profile</h2>
                        <p>Your profile is where you'll enter your bio, major (if you have one), hometown, and other information about yourself. This information is published on the <a href="{{ route('board.meet') }}">Meet the Board</a> page, as well as public locations, for all board members. We'd love for your information to be populated right away if you get elected, so please take the time to make sure your profile is up to date.</p>
                        <p>Note that while board members do have access to this information, <em>it has absolutely no impact on the decision-making process.</em></p>
                        <p>Board candidates need to have the following fields set:</p>
                        <ul>
                            <li>
                                Bio
                                @include('board.panelicon', ['complete' => !empty(Auth::user()->bio)])
                            </li>
                            <li>
                                Hometown
                                @include('board.panelicon', ['complete' => !empty(Auth::user()->hometown)])
                            </li>
                            <li>
                                Pronouns
                                @include('board.panelicon', ['complete' => !empty(Auth::user()->pronouns)])
                            </li>
                            <li>
                                Major (if you don't have one, enter "undecided" or "undeclared")
                                @include('board.panelicon', ['complete' => !empty(Auth::user()->major)])
                            </li>
                        </ul>
                        <a href="{{ route('profile') }}" class="btn btn-lg btn-secondary">Edit profile</a>
                    </div>
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">...</div>
                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">...</div>
                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">...</div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
