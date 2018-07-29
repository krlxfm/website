@php
$special_indicators = ['y' => 'Yes, please try to schedule me here', 'n' => 'No thanks, please avoid scheduling me here', 'm' => 'Meh, doesn\'t matter'];
@endphp
@component('mail::message')
# Congratulations - you've submitted your show!

Thanks for submitting your {{ $show->track->name }} show, "{{ $show->title }}"!
Your application is now complete and ready to be scheduled.

**You can make changes online at any time** until applications close by clicking the button below:

@component('mail::button', ['url' => route('shows.review', $show->id)])
View/Edit Show
@endcomponent

For your convenience, we've incldued a copy of your show application details as they are now.
We will **not** email you copies of any changes, so you'll want to check with the button above to verify the current information that we have on file.

@component('mail::table')
| Field | Value |
| ----- | ----- |
| Track and term | {{ $show->track->name }}, {{ $show->term->name }} |
| Hosts | {{ implode(', ', $show->hosts->pluck('full_name')->all()) }} |
| Title | {{ $show->title }} |
| Description | {{ $show->description }} |
@foreach($show->track->content as $field)
| {{ $field['title'] }} | {!! implode('<br>', $show->content[$field['db']]) !!} |
@endforeach
@if($show->track->weekly)
| Preferred length | {{ $show->preferred_length }} minutes |
@foreach(config('defaults.special_times') as $id => $zone)
| $zone['name'] | {{ $special_indicators[$show->special_times[$id]] }} |
@endforeach
| Classes | {{ implode(', ', $show->classes) }} |
@endif
@endcomponent

Thanks,<br>
KRLX-FM
@endcomponent
