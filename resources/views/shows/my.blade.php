@extends('layouts.missioncontrol', ['title' => 'My Shows'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>My Shows</h1>
                <div class="btn-group ml-auto">
                    <a href="{{ route('shows.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Show</a>
                    <a href="#" class="btn btn-outline-primary"><i class="fas fa-user-plus"></i> Join Show</a>
                </div>
            </div>
            @include('components.term-selector', ['root' => route('shows.my.other')])
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <div class="card">
                <h4 class="card-header bg-warning">
                    Applications in progress
                </h4>
                <div class="list-group list-group-flush">
                    @forelse($incomplete_shows as $show)
                        <a class="list-group-item d-flex align-items-center text-dark" href="{{ route('shows.review', $show) }}">
                            <div>
                                @if($show->boosted)
                                    <span class="badge badge-danger mb-2">
                                        <i class="fas fa-rocket"></i> PRIORITY BOOST
                                    </span>
                                @endif
                                <h5 class="head-sans-serif mb-0">
                                    <strong>{{ $show->title }}</strong>
                                </h5>
                                {{ $show->track->name }} show -
                                @if($show->hosts()->count() == 1)
                                    Just you
                                @else
                                    You plus {{ $show->hosts()->count() - 1 }} {{ str_plural('other', $show->hosts()->count() - 1) }}
                                @endif
                            </div>
                            <div class="ml-auto">
                                <i class="fas fa-chevron-right fa-2x text-muted"></i>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item">
                            <strong>No incomplete applications!</strong> Why not create one?
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <h4 class="card-header bg-success text-white">
                    Completed applications
                </h4>
                <div class="list-group list-group-flush">
                    @forelse($completed_shows as $show)
                        <a class="list-group-item d-flex align-items-center text-dark" href="{{ route('shows.review', $show) }}">
                            <div>
                                @if($show->boosted)
                                    <span class="badge badge-danger mb-2">
                                        <i class="fas fa-rocket"></i> PRIORITY BOOST
                                    </span>
                                @endif
                                <h5 class="head-sans-serif mb-0">
                                    <strong>{{ $show->title }}</strong>
                                </h5>
                                {{ $show->track->name }} show -
                                @if($show->hosts()->count() == 1)
                                    Just you
                                @else
                                    You plus {{ $show->hosts()->count() - 1 }} {{ str_plural('other', $show->hosts()->count() - 1) }}
                                @endif
                            </div>
                            <div class="ml-auto">
                                <i class="fas fa-chevron-right fa-2x text-muted"></i>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item">
                            <strong>No completed applications yet.</strong> When you finish one, it will appear here.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @if($invitations->count() > 0)
            <div class="col-md">
                <div class="card">
                    <h4 class="card-header bg-info text-white">
                        Pending invitations
                    </h4>
                    <div class="list-group list-group-flush">
                        @foreach($invitations as $show)
                            <div class="list-group-item d-flex align-items-center">
                                <div>
                                    @if($show->boosted)
                                        <span class="badge badge-danger mb-2">
                                            <i class="fas fa-rocket"></i> PRIORITY BOOST
                                        </span>
                                    @endif
                                    <h5 class="head-sans-serif mb-0">
                                        <strong>{{ $show->title }}</strong>
                                    </h5>
                                    {{ $show->track->name }} show (ID <code>{{ $show->id }}</code>)
                                </div>
                                <div class="ml-auto">
                                    <i class="fas fa-chevron-right fa-2x text-muted"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
