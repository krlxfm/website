@extends('layouts.missioncontrol', ['title' => 'Board Applications'])

@php
$colors = ['table', 'table-danger', 'table-warning', 'table-success'];
@endphp

@section('mid')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">Interview Schedule</h1>
            <div class="alert alert-info">
                This screen is designed to work best on a desktop computer. Mobile devices will likely not display all data correctly.
            </div>
            <table class="table table-hover table-responsive">
                <thead>
                    <th>Candidate&nbsp;&amp;&nbsp;positions</th>
                    @foreach($dates as $date)
                        @if (! $loop->first and $loop->index % 8 == 0)
                            <th>Candidate&nbsp;&amp;&nbsp;positions</th>
                        @endif
                        <th class="text-center">{!! $date->format('D&\n\b\s\p;n/j, H:i') !!}</th>
                    @endforeach
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        @if (! $loop->first and $loop->index % 12 == 0)
                            <th>Candidate&nbsp;&amp;&nbsp;positions</th>
                            @foreach($dates as $date)
                                @if (! $loop->first and $loop->index % 8 == 0)
                                    <th>Candidate&nbsp;&amp;&nbsp;positions</th>
                                @endif
                                <th class="text-center">{!! $date->format('D&\n\b\s\p;n/j, H:i') !!}</th>
                            @endforeach
                        @endif
                        <tr>
                            <td style="white-space: nowrap;" class="{{ $app->submitted ? '' : 'table-warning' }}">{{ $app->user->full_name }}
                                @foreach($app->positions->pluck('position') as $pos)
                                    <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                @endforeach
                            </td>
                            @foreach($dates as $date)
                                @if (! $loop->first and $loop->index % 8 == 0)
                                    <td class="text-center {{ $app->submitted ? '' : 'table-warning' }}" style="white-space: nowrap;">{{ $app->user->full_name }}
                                        @foreach($app->positions->pluck('position') as $pos)
                                            <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                        @endforeach
                                    </td>
                                @endif
                                <td class="text-center {{ $colors[$app->interview_schedule[$date->format('Y-m-d H:i:s')]] }}">
                                    <input type="radio" name="{{ $app->user->email }}" value="{{ $date->format('Y-m-d H:i:s') }}">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
