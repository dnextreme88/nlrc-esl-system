<x-mail::message>
    # Greetings!
    <br /><br />

    Hi {{ $user->first_name }} {{ $user->last_name }},
    <br /><br />

    @if (in_array($user->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]))
        A student has booked a meeting with you! If you need a refresher, check the meeting details below!
    @elseif ($user->role->name == \App\Enums\Roles::STUDENT->value)
        Congratulations! You have successfully booked a meeting with a teacher. If you need a refresher, check the meeting details below!
    @endif
    <br /><br />

    **Meeting Date**: {{ \Carbon\Carbon::parse($meeting['start_time'], 'UTC')->setTimezone($user->timezone)->format('F j, Y') }}
    **Duration**: {{ \Carbon\Carbon::parse($meeting['start_time'], 'UTC')->setTimezone($user->timezone)->format('g:i A') }} ~ {{ \Carbon\Carbon::parse($meeting['end_time'], 'UTC')->setTimezone($user->timezone)->format('g:i A') }}
    **Your timezone**: {{ $user->timezone }}

    ---
    Login to [esl.nlrc.ph](https://esl.nlrc.ph) to view your meetings. If your timezone is incorrect, please go to your settings once logged in and change it from there.

    **Do not reply** to this email as it is automatically generated and this address is not monitored.
</x-mail::message>
