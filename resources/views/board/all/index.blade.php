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
                <p><strong>You have access to the incomplete application list.</strong> Common question completion status is marked by <i class="far text-muted fa-copyright"></i>/<i class="fas text-success fa-copyright"></i>. Positions are listed in order of preference and display as a white badge while incomplete; they will change to the position color (from <a href="{{ route('board.positions') }}">the position list</a>) once all questions for that position have been answered.</p>
            @endif
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-50">Candidate</th>
                        <th class="w-25">Position(s)</th>
                        <th class="w-25">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                        <tr class="{{ $app->submitted ? '' : 'table-warning' }}">
                            <td class="align-middle">{{ $app->user->full_name }}</td>
                            <td class="align-middle">
                                <i class="{{ $app->common_complete ? 'fas' : 'far' }} fa-copyright text-{{ $app->common_complete ? 'success' : 'muted' }}"></i>
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
