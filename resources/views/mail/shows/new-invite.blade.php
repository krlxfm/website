@component('mail::message')
# You've been invited to join a show!

Hi there! Today's your lucky day - {{ $sender->name }} has just invited you to join the {{ $show->track->name }} KRLX radio show _{{ $show->title }}_.

Sound good? You can join up by clicking the button below!

@component('mail::button', ['url' => route('shows.join', $show)])
Join {{ $show->title }}
@endcomponent

**Important: This invitation will expire when applications close. You'll need to accept this invitation before applications close in order to receive experience points and other benefits.**

If the button isn't working, you can also join with this link: [{{ route('shows.join', $show) }}]({{ route('shows.join', $show) }}).

If you aren't interested in joining {{ $show->title }}, you can safely ignore this email.

Thanks,<br>
KRLX-FM
@endcomponent
