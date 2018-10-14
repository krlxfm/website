@extends('layouts.missioncontrol', ['title' => 'Meet the Board'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">Meet the Board</h1>
            <p>Day-to-day operation of KRLX is governed by the Board of Directors, a group of {{ $board->count() }} students dedicated to keeping the lights on and the radio broadcasting. If you have questions about a particular position or function of KRLX, feel free to reach out to that board member. If you're not sure who to contact, the general inbox <code>{{ 'board@'.env('MAIL_DOMAIN', 'example.com') }}</code> forwards to everyone.</p>
            <p>Looking to join the board? Elections for all seats happen every winter term and interim appointments happen periodically as board members go abroad. Contact the Station Manager with questions.</p>
            <div class="card-columns">
                @foreach($board as $member)
                    <div class="card">
                        <img class="card-img-top" src="{{ $member->photo }}" alt="{{ $member->full_name }}">
                        <div class="card-body">
                            <h5 class="card-title mb-1 head-sans-serif"><strong>{{ $member->full_name }}</strong></h5>
                            <p class="card-text">
                                <strong>{{ $member->title }}</strong><br>
                                Major: {{ $member->major ?? 'Undeclared' }}
                            </p>
                            <p class="card-text">
                                @if($member->bio)
                                    {{ $member->bio }}
                                @else
                                    {{ $member->first_name }} does not have a bio. How sad.
                                @endif
                            </p>
                            <a href="mailto:{{ $member->email }}" class="card-link">Email {{ $member->first_name }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
