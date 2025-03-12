@component('mail::message')
# Hello {{ $package->name }} {{ $package->lastName }},

You have a package on the way!

**Tracking Reference:** {{ $package->reference }}

You can track your package at any time using the link below:

@component('mail::button', ['url' => url('/track/' . $package->reference)])
Track Your Package
@endcomponent

Thank you for using our service!

Best regards,  
{{ config('app.name') }}
@endcomponent
