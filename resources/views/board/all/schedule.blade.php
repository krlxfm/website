@extends('layouts.basic', ['title' => 'Interview Schedule'])

@section('content')
    <div class="row">
        <div class="col">
            <h1>Interview Schedule</h1>
            @foreach($dates as $key => $times)
                <div class="{{ $loop->first ? '' : 'pdf-page-break'}}">
                    <h3 class="mb-3">{{ \Carbon\Carbon::parse($key)->format('l, F j, Y') }}</h3>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="w-25">Time</th>
                                <th class="w-50">Candidate</th>
                                <th class="w-25">Position(s)</th>
                            </tr>
                        </thead>
                        @foreach($times as $time)
                            <tr>
                                <td>{{ $time->format('g:i a') }}</td>
                                @if($apps->where('interview', $time->format('Y-m-d H:i:s'))->count() > 0)
                                    @php
                                        $app = $apps->where('interview', $time->format('Y-m-d H:i:s'))->first();
                                    @endphp
                                    <td>
                                        {{ $app->user->full_name }}
                                        @if ($app->user->hasRole('board'))
                                            <i class="fas fa-star"></i>
                                        @endif
                                        @if ($app->remote)
                                            <i class="fas fa-video"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($app->positions as $pos)
                                            <span class="badge badge-{{ $pos->position->dark ? 'dark' : 'light' }} align-middle" style="background: {{ $pos->position->color }}">{{ $pos->position->abbr }}</span>
                                        @endforeach
                                    </td>
                                @else
                                    <td style="background-color: #ddd !important" colspan="2">
                                        Break
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
        </div>
    </div>
@endsection
