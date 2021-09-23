@extends('layouts.missioncontrol', ['title' => 'My Account'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>My account</h1>
            <p>Fields marked with <i class="fab fa-markdown"></i> support <a href="https://daringfireball.net/projects/markdown/syntax">Markdown formatting</a>. Fields marked with <span class="text-danger">*</span> are required.</p>
            <form action="{{ url("/welcome") }}" method="post">
                @include('partials.profileform')
                <input type="hidden" name="source" value="profile">
                <p>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Save</button>
                </p>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/onboard.js" defer></script>
@endpush
