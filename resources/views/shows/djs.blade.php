@extends('layouts.missioncontrol', ['title' => 'DJ Roster'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>DJ roster ({{ $users->count() }})</h1>
                <div class="btn-group ml-auto">
                    <a href="{{ route('shows.downloadRoster', $term) }}" class="btn btn-primary"><i class="fas fa-download"></i> Download CSV</a>
                </div>
            </div>
            @include('components.term-selector', ['root' => route('shows.djs')])
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th class="w-50">Name and shows</th>
                        <th>Contact information</th>
                        <th class="d-none d-md-table-cell" style="width: 150px">XP before term</th>
                        <th style="width: 90px">XP now</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $oldPriority = $user->priorityAsOf($term->id);
                            $priorityNow = $user->priority;
                        @endphp
                        <tr>
                            <td class="align-middle">
                                <h5 class="mb-1">{{ $user->full_name }}</h5>
                                <ul class="mb-0">
                                    @foreach($user->shows()->where([['submitted', true], ['term_id', $term->id ]])->get() as $show)
                                        <li>
                                            <a href="{{ route('shows.review', $show) }}">{{ $show->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="align-middle">
                                {{ $user->email }}<br>
                                {{ $user->phone_number }}
                            </td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($oldPriority->zone()) }}" style="font-size: x-large;">{{ $oldPriority->terms }}</td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($priorityNow->zone()) }}" style="font-size: x-large;">{{ $priorityNow->terms }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
