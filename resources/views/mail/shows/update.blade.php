@component('mail::message')
# Your show time has changed.

Hi there,

Your show time for _{{ $show->title }}_ has been changed.

Your new time is **{{ $show->day }}s, {{ $show->start->format('g:i a') }} to {{ $show->end->format('g:i a') }}.**
Your first show will be {{ $first_show }}.

Please double check that this new time works for you.
If you have a legitimate reason why this new time does not work for you, please reply-all to this email as soon as possible and we can try to get you sorted out.
Remember that reschedule requests must be submitted by {{ $schedule_lock->format('g:i a') }} on {{ $schedule_lock->format('l, F j') }}.

For your information, we make every effort to minimize the number of shows affected by reschedule requests.
If you didn't request a reschedule for {{ $show->title }}, then your time was adjusted to accommodate a reschedule request submitted by someone else.

Thank you for your cooperation and understanding as we work on creating the best schedule for everyone.
We hope we don't have to change your time on you again, but we will let you know if it happens.
Please don't hesitate to reach out if you have any questions.

{{ config('app.name') }} Scheduling Team
@endcomponent
