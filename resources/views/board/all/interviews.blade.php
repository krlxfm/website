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
        </div>
    </div>
    <div class="form-row">
        <div class="col-4">
            <table class="table">
                <thead>
                    <th class="w-75"><br>Candidate &amp; positions</th>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr>
                            <td>{{ $app->user->full_name }}
                                @foreach($app->positions->pluck('position') as $pos)
                                    <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-8">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        @foreach($dates as $date)
                            <th class="text-center">{!! $date->format('D&\n\b\s\p;n/j, H:i') !!}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr>
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
