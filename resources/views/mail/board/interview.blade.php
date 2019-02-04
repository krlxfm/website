@component('mail::message')
Hello {{ $app->user->first_name }},

Thank you for applying to the KRLX Board of Directors! We are excited to invite you to the next stage of the board elections process, which is a short interview with the current board.

We have assigned you to an interview time based on the scheduling information you put in your application, along with the positions you are applying for. Please let us know immediately if this time does not work.

Your interview time is: **{{ $app->interview->format('l, F j, Y \a\t g:i a') }} Central Time.**<br>
You will be interviewing for the following {{ str_plural('position', $app->positions->count()) }}: **{{ implode(', ', $app->positions->pluck('position.title')->all()) }}**

Be on the lookout for a separate email from the Station Manager with additional information on the interview process, including interview location and what to expect day-of. If you have any questions in the meantime, please don't hesitate to reach out to the Station Manager.

@if ($app->remote)
An IT engineer will also be in contact with you soon to make sure we're able to contact you using the video conference information you provided in your application. _Please make sure you know what time your interview is in your local time zone, as the time listed above is in US/Central._
@endif

Thanks again for your application, and we're looking forward to meeting you soon!

Best,<br>
KRLX-FM Board of Directors
@endcomponent
