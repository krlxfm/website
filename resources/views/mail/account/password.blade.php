@component('mail::message')
# Get back in there!

We have received a request to reset the password on your account with KRLX.
To complete the reset process, please click the button below.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

If the button isn't working, try copying and pasting this link into your browser instead: [{{$url}}]({{$url}})

For your security, this password reset request will expire in one hour.

If you did not request a password reset, no further action is required.

Thanks,<br>
KRLX-FM
@endcomponent
