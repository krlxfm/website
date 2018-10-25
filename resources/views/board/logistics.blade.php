<h2>Logistics</h2>
<p>Also known as: let's figure out how we'll be working together to conduct your interview, and if you're planning on going abroad in the next three terms.</p>
<div class="alert alert-danger">
    In order to retain your board seat, you must be on campus for two out of the next three academic terms. If you will be off campus for two terms next year, <strong>STOP HERE</strong> and <a href="{{ 'mailto:manager@'.env('MAIL_DOMAIN', 'example.org') }}" class="alert-link">contact the Station Manager</a> immediately to discuss your options.
</div>

@if ($logistics_needed)
    <a href="{{ route('board.logistics', $app->year) }}" class="btn btn-lg btn-secondary">Answer the logistics questions <i class="fas fa-chevron-right"></i></a>
@else
    <p><a href="{{ route('board.logistics', $app->year) }}" class="btn btn btn-secondary">Revise logistics questions <i class="fas fa-chevron-right"></i></a></p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Interview method</td>
                <td>
                    @if($app->remote)
                        <strong>Video conference</strong>
                        <br>
                        @switch($app->remote_platform)
                            @case('facebook-messenger')
                                <i class="fab fa-facebook-messenger fa-fw"></i> Facebook Messenger
                                @break
                            @case('skype')
                                <i class="fab fa-skype fa-fw"></i> Skype
                                @break
                            @case('google-hangouts')
                                <i class="fas fa-video fa-fw"></i> Google Hangouts
                                @break
                            @default
                                <i class="fas fa-video fa-fw"></i> Something else
                                @break
                        @endswitch
                        ({{ $app->remote_contact ?? 'contact information unknown' }})
                        <br>
                        <i class="fas fa-phone fa-fw"></i> We will call you at your assigned interview time.
                    @else
                        <strong>In-person, on campus</strong>
                    @endif
                </td>
            </tr>
            <tr>
                <td>OCS plans</td>
                <td>
                    Spring {{ $app->year }}: {{ $app->ocs == 'abroad_sp' ? 'Abroad' : 'On campus' }}<br>
                    Fall {{ $app->year }}: {{ $app->ocs == 'abroad_fa' ? 'Abroad' : 'On campus' }}<br>
                    Winter {{ $app->year + 1 }}: {{ $app->ocs == 'abroad_wi' ? 'Abroad' : 'On campus' }}
                </td>
            </tr>
            <tr>
                <td>Available interview times</td>
                <td>
                    {!! implode('<br>', collect($app->interview_schedule)->filter(function($time) { return $time == 3; })->keys()->map(function($key) { return \Carbon\Carbon::parse($key)->format('D, M j, g:i a'); })->all()) !!}
                </td>
            </tr>
            <tr>
                <td>"If-need-be" interview times</td>
                <td>
                    {!! implode('<br>', collect($app->interview_schedule)->filter(function($time) { return $time == 2; })->keys()->map(function($key) { return \Carbon\Carbon::parse($key)->format('D, M j, g:i a'); })->all()) !!}
                </td>
            </tr>
        </tbody>
    </table>
    <a href="{{ route('board.logistics', $app->year) }}" class="btn btn btn-secondary">Revise logistics questions <i class="fas fa-chevron-right"></i></a>
@endif
