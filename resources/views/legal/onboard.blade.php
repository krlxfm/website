@extends('layouts.missioncontrol', ['title' => 'Welcome aboard!'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>Hello!</h1>
            <p>Welcome to KRLX! You're just a few steps away from activating your account. Please fill in all fields marked with a red asterisk (<span class="text-danger">*</span>), otherwise you are welcome to fill in as much (or as little) of the other fields as you would like.</p>
            <p>Fields marked with <i class="fab fa-markdown"></i> support <a href="https://daringfireball.net/projects/markdown/syntax">Markdown formatting</a>.</p>
            <form action="{{ url("/welcome") }}" method="post">
                @include('partials.profileform')
                <p>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Save and activate</button>
                </p>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/onboard.js" defer></script>
@endpush
