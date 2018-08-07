@extends('layouts.missioncontrol', ['title' => 'Schedule Builder - '.$term->name])

@push('css')
<link href="/css/fullcalendar.min.css" rel="stylesheet">
@endpush

@section('mid')
<div class="form-row">
    <div class="col-md-3 d-none d-md-block">
        <div class="card schedule-full-height">
            <h5 class="card-header">Queue</h5>
            <div class="card-body">
                @for($i = 0; $i < 1000; $i++)
                    <p>{{ $i }}</p>
                @endfor
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" style="border: 0">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 d-none d-md-block">
        <div class="card mb-3 schedule-control-panel">
            <div class="card-body">
                <strong class="text-success"><i class="fas fa-check"></i> No errors here!</strong>
            </div>
        </div>
        <div class="card schedule-short-height" style="line-height: 1.3rem">
            <h5 class="card-header">Radio Show Test</h5>
            <div class="card-body pt-2">
                <p class="mb-1"><small class="text-muted">Standard | SHOW_ID_HERE</small></p>
                <div class="d-flex align-items-start mb-2">
                    <span class="badge bg-priority-a" style="margin-top: 2px">A3</span>
                    <div class="ml-2" style="line-height: 1.3rem">
                        Host One '19<br>
                        Host Two '20
                    </div>
                </div>
                <div class="mb-2">
                    <small><strong>SCHEDULING NOTES</strong></small><br>
                    Geoff is a compsing senior and doesn't like potatoes, so scheduling Monday or Tuesday evenings works best.
                </div>
                <div class="mb-2">
                    <small><strong>BASIC PREFERENCES</strong></small><br>
                    <div class="d-flex align-items-center">
                        <span>Safe Harbor hours</span>
                        <span class="badge badge-success ml-auto">YES</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span>Music Mondays</span>
                        <span class="badge badge-danger ml-auto">NO</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span>Preferred length</span>
                        <span class="badge badge-primary ml-auto">30m</span>
                    </div>
                </div>
                <div class="mb-2">
                    <small><strong>CLASSES</strong></small><br>
                    2a, 3a, 4a
                </div>
                <div class="mb-2">
                    <small><strong>CONFLICTS</strong></small><br>
                    11 conflicts declared
                </div>
                <div class="mb-2">
                    <small><strong>PREFERENCES</strong></small><br>
                    3 preferred times<br>
                    5 strongly preferred times</br>
                    2 first-choice times
                </div>
                <a href="#" class="btn btn-primary btn-block mt-3"><i class="fas fa-external-link-alt"></i> Full application</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="/js/pages/schedule/build.js" defer></script>
@endpush
