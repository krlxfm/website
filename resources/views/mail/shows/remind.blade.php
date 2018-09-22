@component('mail::message')
# Don't forget to submit your application!

This is a friendly reminder that your application for the KRLX radio show {{ $show->title }} has not yet been submitted for sheduling.
If you're hoping to make {{ $show->title }} a reality, there are a couple steps for you to complete.

If you **do not** want {{ $show->title }} to get scheduled, you can either ignore this email or delete the application online.

However, if you **do** want {{ $show->title }} to get scheduled, click the button below to continue your application and submit it.
(Note that your application is not complete until you have clicked the green "Submit" button on the last screen. You will receive a confirmation email when your application has been fully processed.)

@component('mail::button', ['url' => route('shows.review', $show)])
Continue Application for {{ $show->title }}
@endcomponent

If the button isn't working, this link should: [{{ route('shows.review', $show) }}]({{ route('shows.review', $show) }})

Please let us know if you have any questions or run into any trouble!
We look forward to reviewing your application and getting you a slot on the air!

Thanks,<br>
{{ config('app.name') }} Scheduling Team
@endcomponent
