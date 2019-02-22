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
                                <input type="checkbox" class="custom-control-input" id="rescheduling_policy-accept" name="rescheduling_policy">
                                <label class="custom-control-label" for="rescheduling_policy-accept">I have read and agree to the rescheduling policy stated above.<br><strong>I understand that KRLX has the right to refuse a reschedule request if I willingly fail to declare a conflict on my original application.</strong></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="drop_policy-accept" name="drop_policy">
                                <label class="custom-control-label" for="drop_policy-accept">I have read and agree to the show drop policy stated above.<br><strong>I understand that KRLX does not allow shows to be dropped after initial times are assigned except in extreme circumstances.</strong></label>
                            </div>
                        </div>
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
