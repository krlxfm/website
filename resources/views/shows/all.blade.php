@extends('layouts.missioncontrol', ['title' => 'All Shows'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>All Shows</h1>
                <div class="btn-group ml-auto">
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download CSV</a>
                </div>
            </div>
            @include('components.term-selector', ['root' => route('shows.all')])
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th class="w-50">Title and details</th>
                        <th>Hosts</th>
                        <th>Priority</th>
                        <th style="width: 171px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shows->where('submitted', true) as $show)
                        <tr>
                            <td class="align-middle">
                                <h5 class="mb-0">{{ $show->title }}</h5>
                                <small class="text-muted">
                                    {{ $show->id }} | {{ $show->track->name }} | last updated {{ $show->updated_at->toDayDateTimeString() }}
                                </small>
                                <br>
                                {{ $show->description }}
                            </td>
                            <td class="align-middle">
                                {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!}
                            </td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($show->priority[0]) }}">
                                @if($show->boosted and $show->boost == 'S')
                                    <i class="fas fa-rocket"></i>
                                @endif
                                {{ $show->track->prefix }}
                                {{ $show->priority }}
                            </td>
                            <td class="align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('shows.review', $show) }}" class="btn btn-outline-primary">View/Edit</a>
                                    <a href="{{ route('shows.review', $show) }}" class="btn btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex mb-3 mt-5 flex-wrap align-items-center">
                <h2>Incomplete Shows</h2>
                <div class="btn-group ml-auto">
                    <a href="#" class="btn btn-primary"><i class="fas fa-bell"></i> Remind shows</a>
                </div>
            </div>
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th class="w-50">Title and details</th>
                        <th>Hosts</th>
                        <th>Priority</th>
                        <th style="width: 171px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shows->where('submitted', false) as $show)
                        <tr>
                            <td class="align-middle">
                                <h5 class="mb-0">{{ $show->title }} <small><span class="badge badge-warning">INCOMPLETE</span></small></h5>
                                <small class="text-muted">
                                    {{ $show->id }} | {{ $show->track->name }} | last updated {{ $show->updated_at->toDayDateTimeString() }}
                                </small>
                                <br>
                                {{ $show->description }}
                            </td>
                            <td class="align-middle">
                                {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!}
                            </td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($show->priority[0]) }}">
                                {{ $show->track->prefix }}
                                {{ $show->priority }}
                            </td>
                            <td class="align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('shows.review', $show) }}" class="btn btn-outline-primary">View/Edit</a>
                                    <a href="{{ route('shows.review', $show) }}" class="btn btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
