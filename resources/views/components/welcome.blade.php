<div class="p-6 lg:p-8 bg-gradient-to-r from-green-300 to-gray-200 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-600 dark:via-green-700 border-b border-gray-200 dark:border-gray-700">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-800 dark:text-gray-200">
        Welcome, {{ ucfirst(strtolower(Auth::user()->first_name)) }}!
    </h1>

    <p class="mt-6 text-gray-600 dark:text-gray-400 leading-relaxed">This is your dashboard. Some ideas of stuff to put here</p>
</div>

<div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">Reservation of Slots (teachers only)</h2>
        </div>

        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
            Teachers can open/close time slots for when they conduct 1-on-1 sessions with the students who chose that slot. Time intervals should be 12:00 AM - 12:30 AM, 1:00 AM - 1:30 AM etc. The 1st column should be the time slots and the next columns should be the dates, along with the days. Start from the current date until the next 7 days.
        </p>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">Calendar showing the schedules of teacher and student</h2>
        </div>

        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">This calendar is based off the slots reserved, just another component that shows which slots by the teacher and student have been agreed upon.</p>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">Announcements</a></h2>
        </div>

        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
            Site-wide announcements or anything similar pertaining to the entire system. Similar to NLRC-Lumi, but maybe we can improve it a little. Add some tags to the announcements so the users can filter announcements quicker.
        </p>
    </div>
</div>
