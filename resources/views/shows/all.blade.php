@extends('layouts.missioncontrol', ['title' => 'All Shows'])

@php
$code = '';
$position = 0;
@endphp

@section('head')
    <div class="row">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>All Shows ({{ $shows->where('submitted', true)->count() }} submitted)</h1>
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
                        @php
                        if ($code == $show->priority->code()) {
                            $position++;
                        } else {
                            $position = 0;
                            $code = $show->priority->code();
                        }
                        @endphp
                        <tr>
                            <td class="align-middle">
                                <h5 class="mb-0">{{ $show->title }}</h5>
                                <small class="text-muted">
                                    {{ $show->id }} | {{ $show->track->name }}
                                    @if($show->day and $show->start and $show->end)
                                        |
                                        <span class="text-primary">
                                            {{ $show->day }}, {{ Carbon\Carbon::createFromTimeString($show->start)->format('g:i a') }} &ndash; {{ Carbon\Carbon::createFromTimeString($show->end)->format('g:i a') }}
                                        </span>
                                    @endif
                                </small>
                                <br>
                                {{ $show->description }}
                            </td>
                            <td class="align-middle">
                                {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!}
                            </td>
                            <td class="align-middle text-center bg-priority-{{ strtolower($show->priority->zone()) }}">
                                @if($show->boosted)
                                    <i class="fas fa-rocket"></i>
                                @endif
                                {{ $show->track->prefix }}
                                {{ $show->priority->code().str_pad($position, 2, '0', STR_PAD_LEFT) }}
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
            @foreach($one_off_shows as $track)
                <div class="mb-3 mt-5">
                    <h2>{{ $track->first()->track->name }}
                        <small>({{ $track->first()->track->start_day }}s, {{ Carbon\Carbon::createFromTimeString($track->first()->track->start_time)->format('g:i a') }} &ndash; {{ Carbon\Carbon::createFromTimeString($track->first()->track->end_time)->format('g:i a') }})</small>
                    </h2>
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
                        @foreach($track as $show)
                            <tr>
                                <td class="align-middle">
                                    <h5 class="mb-0">{{ $show->title }}
                                        @unless($show->submitted)
                                            <small><span class="badge badge-warning" data-status="incomplete">INCOMPLETE</span></small>
                                        @endunless
                                    </h5>
                                    <small class="text-muted">
                                        {{ $show->id }} | {{ $show->track->name }} | last updated {{ $show->updated_at->toDayDateTimeString() }}
                                    </small>
                                    <br>
                                    {{ $show->description }}
                                </td>
                                <td class="align-middle">
                                    {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!}
                                </td>
                                <td class="align-middle text-center bg-priority-{{ strtolower($show->priority->zone()) }}">
                                    {{ $show->track->prefix }}
                                    {{ $show->priority->code() }}
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
            @endforeach
            @if($shows->where('submitted', false)->count() > 0)
                <div class="d-flex mb-3 mt-5 flex-wrap align-items-center">
                    <h2>Incomplete Shows</h2>
                    <div class="btn-group ml-auto">
                        <button type="button" id="remind" class="btn btn-primary"><i class="fas fa-bell"></i> Remind shows</button>
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
                                    <h5 class="mb-0">{{ $show->title }} <small><span class="badge badge-warning" data-status="incomplete">INCOMPLETE</span></small></h5>
                                    <small class="text-muted">
                                        {{ $show->id }} | {{ $show->track->name }} | last updated {{ $show->updated_at->toDayDateTimeString() }}
                                    </small>
                                    <br>
                                    {{ $show->description }}
                                </td>
                                <td class="align-middle">
                                    {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!}
                                </td>
                                <td class="align-middle text-center bg-priority-{{ strtolower($show->priority->zone()) }}">
                                    @if($show->boosted)
                                        <i class="fas fa-rocket"></i>
                                    @endif
                                    {{ $show->track->prefix }}
                                    {{ $show->priority->code() }}
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
            @endif
        </div>
    </div>
@endsection

@push('js')
<script>
var termID = "{{ $term->id }}";
</script>
<script src="/js/pages/shows/all.js" defer></script>
@endpush
