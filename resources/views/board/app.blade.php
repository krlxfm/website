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
                    <a class="nav-link active" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                        @include('board.panelicon', ['complete' => $missing_fields->count() == 0])
                        Profile
                    </a>
                    <a class="nav-link" id="v-pills-logistics-tab" data-toggle="pill" href="#v-pills-logistics" role="tab" aria-controls="v-pills-logistics" aria-selected="false">
                        @include('board.panelicon', ['complete' => !$logistics_needed])
                        Logistics
                    </a>
                </div>
            </div>
            <div class="col-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        @include('board.profile')
                    </div>
                    <div class="tab-pane fade" id="v-pills-logistics" role="tabpanel" aria-labelledby="v-pills-logistics-tab">
                        @include('board.logistics')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
