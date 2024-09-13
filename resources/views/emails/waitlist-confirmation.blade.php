<x-emails.layout>
Someone added this email to the Oh2Bees Cloud's waitlist. [Click here]({{ $confirmation_url }}) to confirm!

The link will expire in {{ config('constants.waitlist.expiration') }} minutes.

You have no idea what [Oh2Bees Cloud](https://coolify.io) is or this waitlist? [Click here]({{ $cancel_url }}) to remove you from the waitlist.
</x-emails.layout>
