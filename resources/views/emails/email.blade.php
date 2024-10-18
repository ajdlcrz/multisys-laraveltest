<x-mail::message>
<h1>You've Successfully Registered</h1>
<br>
<h1>Email:</h1><p>{{ $emailData['email'] }}</p>

<br>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
