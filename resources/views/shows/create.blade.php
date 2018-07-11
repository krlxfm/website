@extends('layouts.missioncontrol', ['title' => 'Create New Show'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Create New Show</h1>
                <div class="btn-group ml-auto">
                    <a href="{{ route('shows.my') }}" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i> Back to My Shows</a>
                    <a href="#" class="btn btn-outline-secondary"><i class="fas fa-user-plus"></i> Join Show</a>
                </div>
            </div>
            <div class="card my-3">
                <div class="card-header">
                    Start from scratch
                </div>
                <div class="list-group list-group-flush">
                    @forelse($tracks as $track)
                        <a class="list-group-item list-group-item-action d-flex align-items-center" data-track-id="{{ $track->id }}" href="#">
                            <div>
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
@endsection

@push('js')
<script src="/js/pages/shows/create.js" defer></script>
@endpush
