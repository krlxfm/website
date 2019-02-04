@extends('layouts.missioncontrol', ['title' => 'Board Applications'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">Board Applications</h1>
            @if($my_app and !$my_app->submitted)
                <div class="alert alert-warning">
                    <strong>You have an application pending.</strong> In the interest of fairness, please don't review others' applications until you have finished yours. At that point, the correct permissions will be set.
                </div>
            @elseif (!$my_app)
                <div class="alert alert-info">
                    <strong>You do not have an application on file.</strong> If you are eligible to apply for board seats, and plan on doing so, please do not read candidate files until you have submitted your own.
                </div>
            @endif
            @if (Auth::user()->can('view incomplete board applications'))
                <p><strong>You have access to the incomplete application list.</strong> Common question completion status is marked by <i class="far text-muted fa-copyright"></i>/<i class="fas text-success fa-copyright"></i> and interview schedule declaration completion status is marked with <i class="far text-muted fa-calendar"></i>/<i class="fas text-success fa-calendar"></i>. Positions are listed in order of preference and display as a white badge while incomplete; they will change to the position color (from <a href="{{ route('board.positions') }}">the position list</a>) once all questions for that position have been answered.</p>
            @endif
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Flags</th>
                        <th class="w-25">Position(s)</th>
                        <th class="w-25">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr class="{{ $app->submitted ? '' : 'table-warning' }}">
                            <td class="align-middle">
                                {{ $app->user->full_name }}
                                @if($app->interview)
                                    <br>
                                    <small class="text-muted">Interview: {{ $app->interview->format('l, F j, Y, g:i a')}}</small>
                                @endif
                            </td>
                            <td class="align-middle">
                                @unless ($app->submitted)
                                    @php
                                    $profile_done = collect(['bio', 'pronouns', 'hometown', 'major'])->filter(function ($field) use ($app) {
                                        return $app->user->{$field} == null;
                                    })->count() == 0;
                                    @endphp
                                    <i class="{{ $profile_done ? 'fas text-success' : 'far text-muted' }} fa-user"></i>
                                    <i class="{{ $app->common_complete ? 'fas text-success' : 'far text-muted' }} fa-copyright"></i>
                                    <i class="{{ collect($app->interview_schedule)->filter(function($slot) { return $slot == 0; })->count() > 0 ? 'far text-muted' : 'fas text-success' }} fa-calendar"></i>
                                @endunless
                                @if ($app->user->hasRole('board'))
                                    <i class="fas fa-star"></i>
                                @endif
                                @if ($app->remote)
                                    <i class="fas fa-video"></i>
                                @endif
                                @unless ($app->ocs == 'none')
                                    <i class="fas fa-globe-americas"></i>{{ strtoupper(substr($app->ocs, -2)) }}
                                @endunless
                            </td>
                            <td class="align-middle">
                                @foreach($app->positions as $pos)
                                    @if ($pos->complete)
                                        <span class="badge badge-{{ $pos->position->dark ? 'dark' : 'light' }} align-middle" style="background: {{ $pos->position->color }}">{{ $pos->position->abbr }}</span>
                                    @else
                                        <span class="badge badge-light align-middle">{{ $pos->position->abbr }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="align-middle">
                                @if ($app->submitted)
                                    <div class="btn-group">
                                        <a class="btn btn-primary" href="{{ route('board.single', $app->id) }}"><i class="fas fa-download"></i> View/Download</a>
                                    </div>
                                @else
                                    Responses unavailable
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
