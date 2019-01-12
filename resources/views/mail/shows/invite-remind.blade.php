@component('mail::message')
# Your invitation is waiting!

Hi there,

Just a friendly reminder that you have an outstanding invitation to join the KRLX show {{ $show->title }}. If you plan on participating in this show, please accept the invitation with the button below before applications close.

@component('mail::button', ['url' => route('shows.join', $show)])
Join {{ $show->title }}
@endcomponent

If the button is not working, you can also use this link: [{{ route('shows.join', $show) }}]({{ route('shows.join', $show) }})

**Important: If you do not accept this invitation, you will not be eligible for host benefits for {{ $show->title }}, which means you will not appear in the catalog for {{ $show->title }} and you might not be eligible for experience points.** Additionally:

- You must be a trained, active host to sit in the studio by yourself while we're on the air. If you don't accept the invitation, you may need to be accompanied by a host while in the studio.
- If you don't accept the invitation you may not be able to request covers on behalf of {{ $show->title }}, and you may not receive invitations to KRLX community events.
- If you don't accept the invitation your eligibility to request Record Libe card access may be impacted.

Please accept or decline this invitation [here]({{ route('shows.join', $show) }}) before applications close. If you have any questions, you can contact the other show host(s), or any member of the Board of Directors.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
