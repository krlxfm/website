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
            <p>In addition to the applications shown below, the following candidates have applications in progress: {{ implode(', ', $incomplete_apps->pluck('user.full_name')->all()) }}.</p>
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-50">Candidate</th>
                        <th class="w-25">Position(s)</th>
                        <th class="w-25">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completed_apps as $app)
                        <tr>
                            <td>{{ $app->user->full_name }}</td>
                            <td>
                                @foreach($app->positions->pluck('position') as $pos)
                                    <span class="badge badge-{{ $pos->dark ? 'dark' : 'light' }}" style="background: {{ $pos->color }}">{{ $pos->abbr }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary" href="{{ route('board.single', $app->id) }}"><i class="fas fa-download"></i> View/Download</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
