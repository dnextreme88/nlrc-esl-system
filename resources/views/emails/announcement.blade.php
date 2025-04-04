<x-mail::message>
    # Greetings!
    <br /><br />

    Hi {{ $user->first_name }} {{ $user->last_name }},
    <br /><br />

    This is to inform you that an announcement was made through [esl.nlrc.ph](https://esl.nlrc.ph).
    <br /><br />

    ## {{ $announcement->title }}
    Announced on {{ \Carbon\Carbon::parse($announcement['created_at'], 'UTC')->setTimezone($user->timezone)->format('F j, Y g:i:s A') }}

    <br />
    {!! Markdown::parse($announcement->description) !!}
    <br />

    ---
    Login to [esl.nlrc.ph](https://esl.nlrc.ph) to view the announcement.

    **Do not reply** to this email as it is automatically generated and this address is not monitored.
</x-mail::message>
