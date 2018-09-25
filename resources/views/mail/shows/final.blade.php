@component('mail::message')
# Let's do this!

Hey DJs!

The schedule is now finalized and locked, and we are excited to hear {{ $show->title }} on the air in the coming days.
This email contains important contact and policy information, so please read it carefully and let us know if you have any questions.

**Your final show time for {{ $show->title }} is {{ $show->day }}s, {{ \Carbon\Carbon::parse($show->start)->format('g:i a') }} - {{ \Carbon\Carbon::parse($show->end)->format('g:i a') }}, and your first show will be {{ $first_show->format('l, F j') }}.**
@if(\Carbon\Carbon::parse($show->start)->hour <= 3)
Note that this is **{{ $first_show->subDay()->format('l') }} night into {{ $show->day }} morning.** (Please reply-all to this email if you're unsure of what this means.)
@endif

As we will be on the air in less than 24 hours, **schedule change requests are no longer being accepted.**

If you can't make a specific show, you are responsible for finding a cover well in advance.
The best way to do this is to email djs@krlx.org a couple days before your show.
Once you have secured a cover, it is common courtesy to let everyone know by replying to your original message.
_Note that covers must be active DJs — that is, they have to be a host on at least one show this term. Students who do not have a show this term are not allowed to cover for you._

## Policy reminders

While all of the points in the membership agreement are important, we'd like to remind you of a few pertinent items as you prepare for your first show:

- Please arrive on time, every week. Missing or arriving late to too many shows may be grounds for experience point withholding, suspension from KRLX, or other sanctions.
- Fill out operations logs as soon as you arrive, and song logs at the beginning of each song you play. These are both legal requirements and you may be charged with a missed show if you fail to complete them accurately.
- Do not leave the studio unattended at any time, unless the building is being evacuated. This is **ILLEGAL** and will result in immediate termination if caught. If you're flying solo, please use the restroom before your show.
- No controlled substances (including alcohol) in the station. Non-alcoholic beverages are okay in a closed container. Smelly, greasy, and messy foods are not allowed - please eat these (and other snacks, if possible) before your show. If you make (or see) a mess, clean it up quickly - water and electronics don't mix!
- If you notice any equipment malfunctioning, you are required to inform an engineer immediately. If the problem is an emergency (that is, preventing you from legally broadcasting), follow the directions posted in the studio to get in touch with the engineer(s) on call.
- Streaming services may be used, but have restrictions.
  - Spotify: Requires a Spotify Premium subscription. It's illegal to air ads from a non-premium Spotify account.
  - Apple Music (accessed through iTunes): No streaming from the automated or live radio stations. All other usage of the Apple Music catalog is permitted. KRLX pays for a subscription, so please use it as much as you need.
  - YouTube and SoundCloud: _Should not be used except as a last resort._ If you are streaming from your own device, please ensure that you have an ad blocker or have YouTube Premium. (We discourage these sites because the quality of music on them is usually much lower than in official catalogs like Apple Music or Spotify.)
  - Pandora can't be used at all while on the air (though it's a great resource for discovering new music).
  - Other streaming services can be used as long as they don't have ads, and do have good quality music.
- Remember that all material broadcasted on KRLX can be recorded and archived.

## Contact information

**If the show after you is running late:** please try to get a hold of them first before calling the emergency cover line. The phone numbers of the hosts of the show after yours are:

@foreach($show->next->hosts as $dj)
- {{ $dj->name }}: {{ $dj->phone_number }}
@endforeach

Please add these numbers into your contacts and introduce yourself to them.
These numbers will also be available in-studio by pressing the red "HELP!" button on the station computer.

If you can't get a hold of the next show, and it's been at least five minutes since they were scheduled to start, you can request an emergency cover by following the instructions posted in the studio.
_There are Board seats that exist specifically for this purpose - we'll come and save you, even if it's 4:00 in the morning!_

**If _you_ are running late:** Proactively getting in touch with the DJ(s) on air is your best course of action. The best way to do this is call the studio.

Once again, congratulations and welcome aboard!
Please let us know if you have any questions before we go on air!

Thanks,<br>
{{ config('app.name') }} Scheduling Team
@endcomponent
