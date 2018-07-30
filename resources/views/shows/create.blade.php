@extends('layouts.missioncontrol', ['title' => 'Create New Show'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Create New Show</h1>
                <div class="btn-group ml-auto">
                    <a href="{{ route('shows.my') }}" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i> Back to My Shows</a>
                    <a href="{{ route('shows.join') }}" class="btn btn-outline-secondary"><i class="fas fa-user-plus"></i> Join Show</a>
                </div>
            </div>
            <div class="card my-3">
                <div class="card-header">
                    Start from scratch
                </div>
                <div class="list-group list-group-flush">
                    @forelse($tracks as $track)
                        <a class="list-group-item list-group-item-action d-flex align-items-center" data-track-id="{{ $track->id }}" data-track-name="{{ $track->name }}" data-track-title="{{ $track->title_label ?? 'Title' }}" dusk="track-{{ $track->id }}" href="#">
                            <div class="pr-3">
                                <h4 class="card-title mb-1">{{ $track->name }}</h4>
                                <p class="card-text">
                                    {{ $track->description }}
                                    <br><small>
                                        {{ $track->weekly ? 'Weekly' : 'One-time show'}} | {!! $track->boostable ? '<i class="fas fa-check text-success"></i> Eligible' : '<i class="fas fa-times text-danger"></i> Not eligible' !!} for Priority Boost
                                    </small>
                                </p>
                            </div>
                            <i class="fas fa-chevron-right fa-2x text-muted ml-auto"></i>
                        </a>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @component('components.modal')
        @slot('id', 'show-title-modal')
        @slot('title', 'Create Show')
        @slot('action', route('shows.store'))
        @slot('footer')
            <button type="submit" class="btn btn-primary" dusk="create-show" id="create-show">
                Create Show
            </button>
        @endslot
        <input type="hidden" name="track_id" value="0" id="track-input">
        <p>
            To create your show,
            @if($terms->count() > 1)
                choose a term to apply for and
            @endif
            create a working title (which you can change later).
        </p>
        <div class="form-group row">
            <label for="show-title" class="col-sm-4 col-lg-3 col-form-label">Working title</label>
            <div class="col-sm-8 col-lg-9">
                <input type="text" class="form-control" id="show-title" dusk="show-title" placeholder="Working title" name="title">
            </div>
        </div>
        <div class="form-group row">
            <label for="show-term" class="col-sm-4 col-lg-3 col-form-label">Term</label>
            <div class="col-sm-8 col-lg-9">
                @if($terms->count() == 1)
                    <input type="hidden" name="term_id" value="{{ $terms->first()->id }}">
                    <input type="text" readonly class="form-control-plaintext" id="show-term" dusk="term" value="{{ $terms->first()->name }}">
                @else
                    <select class="custom-select" name="term_id" dusk="term-selector">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    @endcomponent
@endsection

@push('js')
<script src="/js/pages/shows/create.js" defer></script>
@endpush
