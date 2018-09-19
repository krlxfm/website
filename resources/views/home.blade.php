@extends('layouts.missioncontrol', ['title' => 'Home'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-1">Mission Control</h1>
            <p class="text-center mb-4">
                {{ $user->name }}, {{ $user->title }} - {{ $user->email }}
                @if(ends_with($user->email, "carleton.edu"))
                    - Priority <span class="badge bg-priority-{{ strtolower($user->priority->zone()) }}">{!! $user->priority->html() !!}</span>
                @endif
            </p>
            @if(count($boosts) > 0)
                <div class="card my-3">
                    <div class="card-header">
                        <h3 class="mb-0">Priority Upgrade Certificates</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($boosts as $boost)
                            <li class="list-group-item d-flex align-items-center flex-wrap">
                                <div>
                                    <h5 class="head-sans-serif mb-0">
                                        <strong>{{ config('defaults.boosts.'.$boost->type) }}</strong>
                                    </h5>
                                    @if($boost->show)
                                        Applied to {{ $boost->show->title }}
                                    @else
                                        <strong class="text-success">Available to redeem</strong>
                                    @endif
                                    @unless($boost->term_id)
                                        | No expiration date
                                    @endunless
                                </div>
                                <div class="ml-auto btn-group">
                                    @if($boost->show)
                                        <a href="#" class="btn btn-outline-success">
                                            <i class="fas fa-exchange-alt"></i> Move
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-success">
                                            <i class="fas fa-certificate"></i> Redeem
                                        </a>
                                    @endif
                                    @if($boost->transferable)
                                        <a href="#" class="btn btn-outline-success">
                                            <i class="fas fa-gift"></i> Transfer
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card my-3">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h3 class="mb-0">Radio shows - {{ $term->name }}</h3>
                    <div class="ml-auto btn-group">
                        <a href="{{ route('shows.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New show
                        </a>
                        <a href="{{ route('shows.my') }}" class="d-none d-md-block btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt"></i> Past terms
                        </a>
                        <a href="{{ route('shows.join') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-plus"></i> Join show
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($shows->where('submitted', true)->count() > 0)
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="mb-0 ml-3 py-3">Completed shows</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="list-group-flush">
                                    @foreach($shows->where('submitted', true) as $show)
                                        <a class="list-group-item list-group-item-action text-dark d-flex align-items-center" href="{{ route('shows.review', $show) }}">
                                            <div>
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
                                            <i class="ml-auto fas fa-chevron-right fa-2x text-muted"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <hr class="my-0">
                    @endif
                    @if($shows->where('submitted', false)->count() > 0)
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="mb-0 ml-3 py-3">Incomplete shows</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="list-group-flush">
                                    @foreach($shows->where('submitted', false) as $show)
                                        <a class="list-group-item list-group-item-action text-dark d-flex align-items-center" href="{{ route('shows.review', $show) }}">
                                            <div>
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
                                            <i class="ml-auto fas fa-chevron-right fa-2x text-muted"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
