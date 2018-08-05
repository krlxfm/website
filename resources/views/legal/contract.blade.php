@extends('layouts.missioncontrol', ['title' => 'Membership Contract'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap mb-3 align-items-center">
                <h1>Membership Contract</h1>
                <span class="d-none d-sm-block ml-auto mr-2">Term</span>
                <span class="d-none d-sm-block" style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $term->id }}</strong></span>
            </div>
            {!! $contract !!}
        </div>
    </div>
@endsection
