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
            <div class="card my-3">
                <div class="card-body py-2">
                    <form class="form-inline">
                        <label class="my-1 mr-2" for="switchTerm">Switch to term:</label>
                        <select class="custom-select my-1 mr-sm-2" id="switchTerm" name="newTerm">
                            @foreach($terms as $new_term)
                                <option value="{{ $new_term->id }}" {{ $new_term->id == $term->id ? 'selected' : '' }}>{{ $new_term->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary my-1">Go</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th class="w-50">Name and shows</th>
                        <th>Contact information</th>
                        <th>XP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="align-middle">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <ul class="mb-0">
                                    @foreach($user->shows as $show)
                                        <li><a href="{{ route('shows.review', $show) }}">{{ $show->title }}</li>
                                    @endforeach
                                    @foreach($user->invitations as $show)
                                        <li><a href="{{ route('shows.review', $show) }}">{{ $show->title }}</a> <small class="text-muted">(invitation pending)</small></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="align-middle">{{ $user->email }}</td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($user->priority->zone()) }}" style="font-size: x-large;">{{ $user->priority->terms }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
