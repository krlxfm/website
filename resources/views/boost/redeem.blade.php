@extends('layouts.missioncontrol', ['title' => 'Redeem Certificate'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>Redeem Certificate</h1>
            <div class="card my-3">
                <div class="card-header">Certificate information</div>
                <div class="card-body">
                    Certificate type: <strong>{{ config('defaults.boosts.'.$boost->type) }}</strong><br>
                    Issued to: {{ $boost->user->full_name }}<br>
                    Issue date: {{ $boost->created_at->toDayDateTimeString() }}<br>
                    Valid for term: {{ $boost->term ? $boost->term->name : "ALL" }}<br>
                    Transferable: {{ $boost->transferable ? "Yes" : "No" }}
                </div>
            </div>
            <p>
                All shows which are eligible to receive this {{ config('defaults.boosts.'.$boost->type) }} are listed below. If the show you wish to upgrade is missing,
                @unless ($boost->type == 'zone')
                    someone else may have already redeemed a {{ config('defaults.boosts.'.$boost->type) }} on it, or
                @endunless
                it may not be eligible for priority upgrades.
            </p>
            <div class="card my-3">
                <ul class="list-group list-group-flush">
                    @foreach($shows as $show)
                        <li class="list-group-item {{ $boost->show_id == $show->id ? 'active' : '' }} d-flex align-items-center flex-wrap">
                            <div>
                                <h5 class="head-sans-serif mb-0">
                                    <strong>{{ $show->title }}</strong>
                                    @unless($show->submitted)
                                        <small><span class="badge badge-warning font-serif">INCOMPLETE</span></small>
                                    @endunless
                                </h5>
                                {{ $show->track->name }} show -
                                @if($show->hosts()->count() == 1)
                                    Just you
                                @else
                                    You plus {{ $show->hosts()->count() - 1 }} {{ str_plural('other', $show->hosts()->count() - 1) }}
                                @endif
                            </div>
                            <div class="ml-auto">
                                @unless($boost->show_id == $show->id)
                                    <form action="{{ route('boost.redeem', $boost->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="show_id" value="{{ $show->id }}">
                                        <button type="submit" class="btn btn-success">
                                            @if($boost->show_id)
                                                <i class="fas fa-exchange-alt"></i> Move here
                                            @else
                                                <i class="fas fa-certificate"></i> Redeem
                                            @endif
                                        </button>
                                    @endunless
                                <a href="{{ route('shows.review', $show->id) }}" class="btn btn-light" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Show details
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
