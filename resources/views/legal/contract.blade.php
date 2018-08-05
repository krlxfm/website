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

            <div class="card my-3">
                <div class="card-body">
                    <form action="/contract" method="post">
                        @csrf
                        <input type="hidden" name="term" value="{{ $term->id }}">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="contract-accept" name="contract">
                                <label class="custom-control-label" for="contract-accept">I have read and agree to the KRLX Membership Agreement for {{ $term->name }}.</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Sign and continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
