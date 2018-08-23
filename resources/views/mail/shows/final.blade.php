@component('mail::message')
# Let's do this!

Hey DJs!

The schedule is now finalized and locked, and we are excited to hear {{ $show->title }} on the air in the coming days.
This email contains important contact and policy information, so please read it carefully and let us know if you have any questions.

**Your final show time for {{ $show->title }} is {{ $show->day }}s, {{ $show->start->format('g:i a') }} - {{ $show->end->format('g:i a') }}, and your first show will be {{ $first_show->format('l, F j') }}.**
Schedule change requests are no longer being accepted, and you are now responsible for finding covers if you can't make this show time in a particular week.

If you can’t make a show, please line up a cover well in advance.
The best way to do this is to email djs@krlx.org a couple days before your show.
Once you have secured a cover, it is common courtesy to let everyone know by replying to your original message.
_Note that covers must be active DJs — that is, they have to be a host on at least one show this term. Students who do not have a show this term can't cover for you._

**Policy reminders:** While all of the points in the membership agreement are important, we'd like to remind you of a few pertinent items as you prepare for your first show:

- Please arrive on time, every week. Missing or arriving late to too many shows may be grounds for experience point withholding, suspension from KRLX, or other sanctions.
- Do not leave the studio unattended at any time, unless the building is being evacuated. This is **ILLEGAL** and will result in immediate termination if caught.
- No food in the studio, and no controlled substances (including alcohol) in the station.
- If you notice any equipment malfunctioning, you are required to inform an engineer immediately. If the problem is an emergency (that is, preventing you from legally broadcasting), follow the directions posted in the studio to get in touch with the engineer(s) on call.
- Streaming services may be used, but have restrictions.
  - Spotify: Requires a Spotify Premium subscription. It’s illegal to air ads from your Spotify.
  - Apple Music: No streaming from the automated or live radio stations. KRLX pays for a subscription, so please use it as much as you need.
  - YouTube and SoundCloud: Should not be used except as a last resort. If you are streaming from your own device, please ensure that you have an ad blocker or have YouTube Premium.
- Remember that all material broadcasted on KRLX can be recorded and archived.

**If the show after you is running late:** please try to get a hold of them first before calling the emergency cover line. The phone numbers of the hosts of the show after yours are:

@foreach($next_show_djs as $dj)
- {{ $dj->name }}: {{ $dj->phone_number }}
@endforeach

If you can't get a hold of the next show, and it's been at least five minutes since they were scheduled to start, you can request an emergency cover by following the instructions posted in the studio.

**If _you_ are running late:** Proactively getting in touch with the DJ(s) on air is your best course of action. You can call the studio, or contact the DJs ahead of you at the following numbers:

@foreach($previous_show_djs as $dj)
- {{ $dj->name }}: {{ $dj->phone_number }}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent
