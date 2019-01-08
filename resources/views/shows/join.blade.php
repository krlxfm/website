@extends('layouts.missioncontrol', ['title' => 'Join Show'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Join Show</h1>
                <span class="d-none d-sm-block ml-auto mr-2">Show ID</span>
                <span class="d-none d-sm-block" style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $show->id }}</strong></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body p-5 text-center">
                    <p>Would you like to join the {{ $show->track->name }}, {{ $show->term->name }} show</p>
                    <h3 class="head-sans-serif mb-3">
                        <strong>{{ $show->title }}</strong>
                    </h3>
                    <p class="mb-1">with
                        @switch($show->hosts->count())
                            @case(0)
                                literally nobody else?
                                @break
                            @case(1)
                                {{ $show->hosts->first()->full_name }}?
                                @break
                            @case(2)
                                {{ $show->hosts->first()->full_name }} and {{ $show->hosts->last()->full_name }}?
                                @break
                            @default
                                {{ $show->hosts->first()->full_name }} and {{ $show->hosts->count() - 1 }} others?
                        @endswitch
                    </p>
                    <p>You'll have the opportunity to review and edit the full application, including schedule, after you accept.</p>

                    <div class="alert alert-warning">
                        <h4><strong>If you accept this invitation, you are accepting responsibility to be in the studio during this show's assigned time slot.</strong></h4>
                        All group members are held equally accountable for a missed show or late arrival, so if you only plan on being on the show occasionally, you should not accept this invitation.
                    </div>

                    <form action="{{ route('shows.join', $show) }}" method="post">
                        @csrf
                        @method('put')
                        <input type="hidden" name="token" value="{{ encrypt(['show' => $show->id, 'user' => Auth::user()->email]) }}">
                        <div class="mt-5">
                            <a class="btn btn-lg btn-outline-secondary" href="/shows">Not now</a>
                            <button type="submit" class="btn btn-lg btn-success">Accept invitation</button>
                        </div>
                        <p class="my-2"><a class="text-danger" href="#" id="decline-invitation">Decline invitation</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
var showID = "{{ $show->id }}";
</script>
<script src="/js/pages/shows/join.js" defer></script>
@endpush
