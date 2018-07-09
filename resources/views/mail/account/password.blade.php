@component('mail::message')
# Get back in there!

Hi there!

Someone -- probably you -- has requested a password reset for your account with KRLX.
No worries, it happens to the best of us!
You can click this button to reset your password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

If the button isn't working, try copying and pasting this link into your browser instead: [{{$url}}]({{$url}})

For your security, this password reset request will expire in one hour.

If you did not request a password reset, no further action is required.

Thanks,<br>
KRLX-FM
@endcomponent
