@php
$special_indicators = ['y' => 'Yes, please try to schedule me here', 'n' => 'No thanks, please avoid scheduling me here', 'm' => 'Meh, doesn\'t matter'];
function md_replace($str) {
    return str_replace(['\\', '*', '_', '~'], ['\\\\', '\*', '\_', '<span>~</span>'], e($str));
}
@endphp
@component('mail::message')
# Congratulations - you've submitted your show!

Thanks for submitting your {{ $show->track->name }} show _{{ $show->title }}_!
Your application is now complete and ready to be scheduled.

**IMPORTANT: Please check that your [host list]({{ route('shows.hosts', $show) }}) is correct!** Hosts can accept invitations at any time while applications are open. If your host list doesn't look right, [try these troubleshooting steps](https://github.com/krlxfm/website/wiki/Group-Shows#troubleshooting).

You can edit this show online at any time until applications close by clicking the button below:

@component('mail::button', ['url' => route('shows.review', $show->id)])
View/Edit Show
@endcomponent

For your convenience, we've included a copy of your show application details as they are now.
We will **not** email you copies of any changes, so you'll want to [check your show online]({{ route('shows.review', $show->id) }}) to verify the current information that we have on file.

@component('mail::table')
| Field | Value |
| ----- | ----- |
| Show ID | {{ $show->id }} |
| Track and term | {{ $show->track->name }}, {{ $show->term->name }} |
| Hosts | {!! implode('<br>', $show->hosts->pluck('full_name')->all()) !!} |
| Title | {!! md_replace($show->title) !!} |
| Description | {!! md_replace($show->description) !!} |
@foreach($show->track->content as $field)
| {{ $field['title'] }} | {!! implode('<br>', array_wrap($show->content[$field['db']])) !!} |
@endforeach
@if($show->track->weekly)
| Preferred length | {{ $show->preferred_length }} minutes |
@foreach(config('defaults.special_times') as $id => $zone)
| {{ $zone['name'] }} | {{ $special_indicators[$show->special_times[$id]] }} |
@endforeach
| Classes | {{ implode(', ', $show->classes) }} |
@include('mail.shows.mailtr', ['title' => 'Conflicts', 'list' => $show->conflicts ])
@include('mail.shows.mailtr', ['title' => 'Preferences', 'list' => $show->preferences ])
@else
| Conflicts | {{ implode(', ', array_map(function($conflict) { return Carbon\Carbon::parse($conflict)->format('F j'); }, $show->conflicts)) }} |
| Preferences | {{ implode(', ', array_map(function($preference) { return Carbon\Carbon::parse($preference)->format('F j'); }, $show->preferences)) }} |
@endif
@foreach($show->track->scheduling as $field)
| {{ $field['title'] }} | {!! implode('<br>', array_wrap($show->scheduling[$field['db']])) !!} |
@endforeach
| Scheduling notes | {!! md_replace($show->notes) !!} |
@endcomponent

## What happens next?

Now that you've submitted your application, there are a few more things that will happen before you can sit in the studio:

- Please continue to [review and edit your application]({{ route('shows.review', $show) }}) - particularly your [schedule]({{ route('shows.schedule', $show) }}) - as needed until applications close. **You are responsible for declaring your full schedule, so please ensure yours is accurate!**
- If you need to drop your show, please do so online before applications close if at all possible.
- Once applications close, we'll work on the schedule and publish it as soon as we can. First schedules are typically released within a few hours of applications closing.
- If you or your hosts need training, they'll receive an email from the Compliance Director with details.
@if($show->track->weekly)
- We'll let you know of your time once the initial schedule is released, as well as anytime your time changes.
- You'll receive one final email about 24 hours before we go on the air, confirming your final show time and containing additional policy and contact information. **Schedule change requests will not be accepted after this point.**
@else
- Because you have applied to a non-weekly track, you will receive more information from a member of the scheduling team directly.
@endif

Please let us know if you have any other questions, otherwise we'll be in touch soon!

Thanks,<br>
KRLX-FM
@endcomponent
