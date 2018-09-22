@extends('layouts.missioncontrol', ['title' => 'Delete Show'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Confirm Deletion</h1>
                <span class="d-none d-sm-block ml-auto mr-2">Show ID</span>
                <span class="d-none d-sm-block" style="font-size: xx-large; padding-bottom: 1px"><strong>{{ $show->id }}</strong></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="alert alert-warning">
                <strong>Careful:</strong> You are about to delete the show {{ $show->title }}. For your convenience, details of the show are copied below. If you're sure you want to delete {{ $show->title }}, scroll to the bottom of the page.
            </div>
            @include('shows.review-table')
            <div class="alert alert-warning">
                <strong>Careful:</strong> deleting a show is not reversible. This is the final step before the show {{ $show->title }} will be deleted <strong>FOREVER.</strong> If you want to keep this show, turn back now!
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('shows.review', $show) }}" class="btn btn-block btn-success">Back to safety</a>
        </div>
        <div class="col">
            <form action="{{ route('shows.destroy', $show) }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-block">Delete {{ $show->title }}</button>
            </form>
        </div>
    </div>
@endsection
