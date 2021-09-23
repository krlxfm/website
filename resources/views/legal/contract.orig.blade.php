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
                    <form action="{{ url("/contract") }}" method="post">
                        @csrf
                        <input type="hidden" name="term" value="{{ $term->id }}">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="phone_verification-accept" name="phone_number_verification">
                                <label class="custom-control-label" for="phone_verification-accept">I confirm that I can be reached at <strong>{{ Auth::user()->phone_number }}</strong>. (If this number is not accurate, you <strong>must</strong> <a href="/profile">update it here</a> before continuing.)</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="rescheduling_policy-accept" name="rescheduling_policy">
                                <label class="custom-control-label" for="rescheduling_policy-accept">I have read and agree to the rescheduling policy stated above. I will ensure that I declare my complete schedule on my application(s), and I understand that I can only request a reschedule in limited circumstances.</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="drop_policy-accept" name="drop_policy">
                                <label class="custom-control-label" for="drop_policy-accept">I understand that KRLX does not allow shows to be dropped after initial times are assigned except in extreme circumstances.</label>
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
