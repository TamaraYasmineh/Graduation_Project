@component('mail::message')
# Login Verification Code

Your OTP code is:

@component('mail::panel')
{{ $otp }}
@endcomponent

This code will expire in 10 minutes.

Thanks,  
{{ config('app.name') }}
@endcomponent
