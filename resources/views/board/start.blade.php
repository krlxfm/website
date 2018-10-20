@extends('layouts.missioncontrol', ['title' => 'Join the Board'])

@section('head')
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-3">Welcome aboard!</h1>
            <p>The KRLX Board of Directors is a group of student volunteers who are dedicated to keeping the lights on and the radio broadcasting. We're honored that you are considering a position on the Board and are excited to work with you throughout the selection process, to help you achieve your goals with KRLX, and to allow us to put the future of the station into the trustworthy hands of its future leaders.</p>
            <p>If you love KRLX and want to help lead it for the next year, you are in the right place. We're ready when you are.</p>
            @can('apply for board seats')
                <p class="text-center">
                    <a href="/board/apply/start" class="btn btn-success btn-lg">Start <span class="d-none d-sm-inline">{{ date('Y') }} - {{ date('Y') + 1 }} Board</span> Application</a>
                </p>
            @else
                <p class="text-center">
                    <button class="btn btn-danger btn-lg" disabled type="button">Application Unavailable</button>
                </p>
                <p class="text-center">You currently do not have access to the Board application. This may be because you don't meet the eligibility requirements, or applications are currently closed. If you believe you should have access to the Board application, please contact the Station Manager or an IT engineer.</p>
            @endcan
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md">
            <h2>How this process works</h2>
            <p>Applying to the Board is a three-step process.</p>
            <ol>
                <li><strong>Review the position(s) you're interested in.</strong> All positions have their responsibilities outlined here, though each member makes the position their own. You are strongly encouraged to read through the list of positions, then <a href="/board/meet">contact the board member(s) who currently hold that position</a> to ask what their experience has been like.</li>
                <li><strong>Fill out an application online.</strong> A Board application consists of short written responses to a series of common questions, and short written responses to questions specific to the position(s) you are interested in. All responses must be submitted online.</li>
                <li><strong>Participate in a short interview.</strong> If you're on campus, you'll interview in person for about 15 minutes with the current Board. If you're abroad, you'll complete your interiew over video conference.</li>
            </ol>
            <p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Unlike radio show applications, you can't edit a board application after you submit it. You can, however, save your progress at any step along the way before you submit it.
                </div>
            </p>
        </div>
        <div class="col-md">
            <h2>Eligiblity requirements</h2>
            <p>All Board seats carry the following eligibility requirements:</p>
            <ul>
                <li>You must be a host on at least one current or past radio show (it's totally okay if this term is your first time hosting a show)</li>
                <li>You must be in good standing with KRLX and the FCC</li>
                <li>You must be a current first-year, sophomore, or junior student</li>
                <li>You must be on campus for at least two of the next three academic terms</li>
                <li>You must not have had your experience points withheld the last time you did a radio show</li>
                <li>You must have a passion for KRLX and a desire for leadership and community service</li>
            </ul>
            <p>Additional eligibility requirements apply for Station Managers:</p>
            <ul>
                <li>Station Manager candidates must have completed at least one term of service on the Board (including interim appointments).</li>
                <li>Station Manager candidates must be on campus for ALL THREE of the next three academic terms.</li>
            </ul>
            <p>If you have questions about your eligibility, please contact the current Station Manager.</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h2 class="text-center mb-3">Ready to get started?</h2>
            @can('apply for board seats')
                <p class="text-center">
                    <a href="/board/apply/start" class="btn btn-success btn-lg">Start <span class="d-none d-sm-inline">{{ date('Y') }} - {{ date('Y') + 1 }} Board</span> Application</a>
                </p>
            @else
                <p class="text-center">
                    <button class="btn btn-danger btn-lg" disabled type="button">Application Unavailable</button>
                </p>
                <p class="text-center">You currently do not have access to the Board application. This may be because you don't meet the eligibility requirements, or applications are currently closed. If you believe you should have access to the Board application, please contact the Station Manager or an IT engineer.</p>
            @endcan
        </div>
    </div>
@endsection
