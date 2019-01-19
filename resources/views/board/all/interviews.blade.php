@extends('layouts.missioncontrol', ['title' => 'Board Applications'])

@php
$colors = ['', 'table-danger', 'table-warning', 'table-success'];
@endphp

@section('mid')
    <div class="row">
        <div class="col">
            <table class="table table-hover table-responsive">
                <thead>
                    <th>Candidate</th>
                    <th>Positions</th>
                    @foreach($dates as $date)
                        <th class="text-center">{!! $date->format('D&\n\b\s\p;n/j, H:i') !!}</th>
                    @endforeach
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr>
                            <td style="white-space: nowrap;">{{ $app->user->full_name }}</td>
                            <td style="white-space: nowrap;">
                                @foreach($app->positions->pluck('position') as $pos)
                                    <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                @endforeach
                            </td>
                            @foreach($dates as $date)
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
