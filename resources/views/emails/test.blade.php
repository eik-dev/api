@component('mail::message')
# Password Recovery

Hello,

You are receiving this email because a password reset request was made for your account.

To reset your password, please click the button below:

@component('mail::button', ['url' => $resetUrl])
Reset Password
@endcomponent

If you did not request a password reset, no further action is required.

Thanks,
{{ config('app.name') }}
@endcomponent