@extends('layouts.missioncontrol', ['title' => 'Board Applications'])

@php
$colors = ['', 'table-danger', 'table-warning', 'table-success'];
function checkedIfTime($app, $time) {
    if ($time === null) {
        return $app->interview === null ? 'checked' : '';
    } else {
        return ($app->interview and $app->interview->format('Y-m-d H:i:s') === $time) ? 'checked' : '';
    }
}
@endphp

@section('mid')
    <div class="row">
        <div class="col">
            <form method="post" id="interviews-form">
                @csrf
                @method('patch')
                <table class="table table-hover table-responsive">
                    <thead>
                        <th>Candidate</th>
                        <th>Positions</th>
                        <th>Unscheduled</th>
                        @foreach($dates as $date)
                            <th class="text-center">{!! $date->format('D&\n\b\s\p;n/j, H:i') !!}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach($apps as $app)
                            <tr>
                                <td style="white-space: nowrap;">
                                    {{ $app->user->full_name }}
                                    @if ($app->user->hasRole('board'))
                                        <i class="fas fa-star"></i>
                                    @endif
                                    @if ($app->remote)
                                        <i class="fas fa-video"></i>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    @foreach($app->positions->pluck('position') as $pos)
                                        <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="interviews[{{ $app->id }}]" value="" {{ checkedIfTime($app, null) }}>
                                </td>
                                @foreach($dates as $date)
                                    <td class="text-center {{ $colors[$app->interview_schedule[$date->format('Y-m-d H:i:s')]] }}"
                                        data-html="true"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="<strong>{{ $app->user->full_name . '</strong><br>'.implode(', ', $app->positions->pluck('position.abbr')->all()) .'<br>'. $date->format('D n/j, g:i a') }}">
                                        @if($app->interview_schedule[$date->format('Y-m-d H:i:s')] == 1)
                                            <i class="fas fa-times text-danger"></i>
                                        @else
                                            <input type="radio" name="interviews[{{ $app->id }}]" value="{{ $date->format('Y-m-d H:i:s') }}" {{ checkedIfTime($app, $date->format('Y-m-d H:i:s')) }}>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>
                    <input type="hidden" name="notify" value="0" id="notify-field">
                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
                    <button type="button" class="btn btn-lg btn-info" id="save-with-email">Save and Notify Candidates</button>
                </p>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/board/interviews.js" defer></script>
@endpush
