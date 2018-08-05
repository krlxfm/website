@extends('layouts.missioncontrol', ['title' => 'DJ Roster'])

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>DJ roster</h1>
                <div class="btn-group ml-auto">
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download CSV</a>
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
                        <th class="d-none d-md-table-cell" style="width: 200px">XP as of {{ $term->id }}</th>
                        <th style="width: 90px">XP Now</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
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
                            <td class="align-middle">{{ $user->email }}</td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($user->priorityAsOf($term->id)->zone()) }}" style="font-size: x-large;">{{ $user->priorityAsOf($term->id)->terms }}</td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($user->priority->zone()) }}" style="font-size: x-large;">{{ $user->priority->terms }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
