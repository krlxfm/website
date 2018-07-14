@extends('layouts.showapp', ['title' => 'Hosts', 'next' => 'content'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text">If you will be doing this show with others, you can invite your co-host(s) here. Each co-host must sign in to the website and accept the invitation in order to be listed on the show, receive experience points, count towards show priority, or be eligible to cover other shows.</p>
                </div>
            </div>
            <p>
                <button class="btn btn-block btn-lg btn-dark">
                    <i class="fas fa-user-plus"></i> Invite a host
                </button>
            </p>
            <participant-list></participant-list>
        </div>
    </div>
@endsection

@push('js')
<script>
var showID = "{{ $show->id }}";
var userID = {{ Auth::user()->id }};
var participants = {!! json_encode($show->hosts->merge($show->invitees)) !!};
</script>
<script src="/js/pages/shows/hosts.js" defer></script>
@endpush
