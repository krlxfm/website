@extends('layouts.basic', ['title' => 'Application File'])

@php
$icons = ['skype' => 'skype', 'facebook' => 'facebook-messenger', 'google-hangouts' => 'video'];
@endphp

@section('content')
    <div class="d-print-none">
        <div class="container mt-3">
            <div class="row">
                <div class="col">
                    <div class="alert alert-info">
                        <p>This application has been formatted to work best as a printed PDF. If your browser doesn't have a dedicated "Export as PDF"/"Save as PDF" option, you can choose PDF as your print destination in the Print dialog box. <strong>When printing, please ensure that "Print backgrounds" is checked, otherwise some parts of the PDF may not appear correctly.</strong> (Don't worry, this box won't appear at all in printing!)</p>
                        <p class="mb-2"><a href="{{ route('board.all') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i> Back to all applications</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <h1 class="head-sans-serif"><strong>{{ $app->user->full_name }}</strong></h1>
            <p class="lead">
                PDF generated for {{ Auth::user()->full_name }} at {{ date('Y-m-d H:i:s') }}
            </p>
            <hr>
            <p class="mb-0">
                Pronouns: {{ $app->user->pronouns }}<br>
                Major: {{ $app->user->major ?? 'Undeclared' }}<br>
                Hometown: {{ $app->user->hometown ?? 'Hometown not set' }}<br>
                Interview method: {{ $app->remote ? 'Video conference -' : 'In person'}}
                @if($app->remote)
                    <i class="fas fa-{{ $icons[$app->remote_platform]}}"></i> {{ $app->remote_contact }}
                @endif
                <br>
                OCS plans:
                @switch($app->ocs)
                    @case('none')
                        On campus all three terms
                        @break
                    @case('abroad_sp')
                        Abroad in spring (will need interim appointment immediately)
                        @break
                    @case('abroad_fa')
                        Abroad next fall
                        @break
                    @case('abroad_wi')
                        Abroad next winter (a year from now)
                        @break
                @endswitch
                <br>
                Radio priority: {!! $app->user->priority->html() !!} ({{ $app->user->priority->terms }} {{ str_plural('term', $app->user->priority->terms) }})
            </p>
        </div>
        <div class="col-3">
            <img class="img-fluid" src="{{ config('defaults.directory').explode('@', $app->user->email)[0] }}" alt="{{ $app->user->full_name }}">
        </div>
    </div>
    <hr class="border-dark">
    <p class="mb-0">
        @if (strlen($app->user->bio) > 2000)
            {{ trim(substr($app->user->bio, 0, 2000)) }}...
        @else
            {{ $app->user->bio }}
        @endif
    </p>
    <hr class="border-dark">
    <div class="row pdf-page-break">
        <div class="col-5">
            <h2 class="head-sans-serif">Positions</h2>
            <p>Positions are listed in order of preference.</p>
            <ul class="list-group">
                @foreach($app->positions->pluck('position') as $position)
                    <li class="list-group-item {{ (in_array($position->id, $redacted_sections->all()) ? 'list-group-item-danger' : '') }}">
                        {{ $position->title }}
                        @if(in_array($position->id, $redacted_sections->all()))
                            (redacted)
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-7">
            <h2 class="head-sans-serif">Notices</h2>
            <p><strong>The contents of this application file are considered private and are not to be disclosed to anyone outside of the KRLX Board of Directors without the express written permission of the candidate, except where disclosure is required by law.</strong> KRLX Board application files are not college confidential. Please use good judgement if discussing candidate files outside of the context of official board election proceedings. If you download or print a PDF of this application, you're responsible for keeping it safe.</p>
            <p class="mb-0">To reduce the risk of a conflict of interest, candidate files may be redacted in whole or in part for some members of the Board. Redacted sections, if there are any, are indicated with a red background in the list on the left. Our policy is to redact sections in direct competition; that is, if you and a candidate are applying for a common seat, you will not be able to see their responses to that seat. (You will be able to see their common answers and answers to seats for which you are not competing.)</p>
        </div>
    </div>
    <div class="pdf-page-break">
        <div class="bg-light border border-dark mt-1 py-2 px-3 mb-3">
            <h2 class="mb-0">Common questions</h2>
        </div>
        @foreach($app->common as $question => $response)
            <div>
                <h5 class="head-sans-serif"><strong>{{ $question }}</strong></h5>
                {!! $response !!}
            </div>
        @endforeach
    </div>
    @foreach($app->positions as $position)
        <div class="pdf-page-break">
            <div style="background: {{ $position->position->color }}; color: {{ $position->position->dark ? 'white' : 'black'}}" class="mt-1 py-2 px-3 mb-3">
                <h2 class="mb-0">{{ $position->position->title }}</h2>
            </div>
            @if(in_array($position->position_id, $redacted_sections->all()))
                <div class="alert alert-danger">
                    Responses in this section have been redacted.
                </div>
            @endif
            @foreach($position->responses as $question => $response)
                <div>
                    <h5 class="head-sans-serif"><strong>{{ $question }}</strong></h5>
                    @if(in_array($position->position_id, $redacted_sections->all()))
                        <p><em>Response redacted.</em></p>
                    @else
                        {!! $response !!}
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
