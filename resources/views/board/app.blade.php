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
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <p class="text-muted text-center mb-1" style="font-variant: small-caps; letter-spacing: 3px; font-weight: bold;">
                        general questions
                    </p>
                    <a class="nav-link active" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                        @include('board.panelicon', ['complete' => $missing_fields->count() == 0])
                        Profile
                    </a>
                    <a class="nav-link" id="v-pills-logistics-tab" data-toggle="pill" href="#v-pills-logistics" role="tab" aria-controls="v-pills-logistics" aria-selected="false">
                        @include('board.panelicon', ['complete' => !$logistics_needed])
                        Logistics
                    </a>
                    <a class="nav-link" id="v-pills-common-tab" data-toggle="pill" href="#v-pills-common" role="tab" aria-controls="v-pills-common" aria-selected="false">
                        @include('board.panelicon', ['complete' => !$common_needed])
                        Common questions
                    </a>
                    <p class="text-muted text-center mb-1 mt-3" style="font-variant: small-caps; letter-spacing: 3px; font-weight: bold;">
                        position specific questions
                    </p>
                    <a class="nav-link" id="v-pills-add-tab" data-toggle="pill" href="#v-pills-add" role="tab" aria-controls="v-pills-add" aria-selected="false">
                        <i class="fas fa-plus fa-fw"></i>
                        Add a position
                    </a>
                    @if($app->positions->count() > 1)
                    <a class="nav-link" id="v-pills-common-tab" data-toggle="pill" href="#v-pills-common" role="tab" aria-controls="v-pills-common" aria-selected="false">
                        <i class="fas fa-random fa-fw"></i>
                        Reorder positions
                    </a>
                    @endif
                    @foreach($app->positions as $position)
                        <a class="nav-link" id="v-pills-common-tab" data-toggle="pill" href="#v-pills-common" role="tab" aria-controls="v-pills-common" aria-selected="false">
                            {{ $position->position->title }}
                        </a>
                    @endforeach
                    <p class="text-muted text-center mb-1 mt-3" style="font-variant: small-caps; letter-spacing: 3px; font-weight: bold;">
                        review and submit
                    </p>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        @include('board.profile')
                    </div>
                    <div class="tab-pane fade" id="v-pills-logistics" role="tabpanel" aria-labelledby="v-pills-logistics-tab">
                        @include('board.logistics')
                    </div>
                    <div class="tab-pane fade" id="v-pills-common" role="tabpanel" aria-labelledby="v-pills-common-tab">
                        @include('board.common')
                    </div>
                    <div class="tab-pane fade" id="v-pills-add" role="tabpanel" aria-labelledby="v-pills-add-tab">
                        @include('board.add')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
