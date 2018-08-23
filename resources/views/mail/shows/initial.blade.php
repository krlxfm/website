@component('mail::message')
# You've got a time slot!

Thanks for submitting your {{ $show->track->name }} show _{{ $show->title }}_.
We are pleased to inform you that your show has been assigned a time!

Your **INITIAL** time is **{{ $show->day }}s, {{ $show->start->format('g:i a') }} to {{ $show->end->format('g:i a') }}.** Your first show will be {{ $first_show }}.

Other important dates:

- Schedule change requests due: {{ $schedule_lock->format('l, F j, g:i a') }}
- On air: {{ $show->term->on_air->format('l, F j, g:i a') }}
- Off air: {{ $show->term->off_air->format('l, F j, g:i a') }}

Please note that this is a draft schedule, and that times are subject to change.
If your time does change, we'll let you know right away - though we hope (just as much as you do!) that it stays where it is.

**If you need to change your show time, please reply-all to this email as soon as you can** and we'll see what we can do to get you sorted out.
Schedule changes MUST be submitted by {{ $schedule_lock->format('g:i a') }} on {{ $schedule_lock->format('l, F j') }}, though the earlier you submit yours, the better our chances are of helping you out.

**If your show time looks good, you don't need to do anything for now.**

Thanks again, and welcome aboard!
Please don't hesitate to reach out if you have any questions.

{{ config('app.name') }} Scheduling Team
@endcomponent
