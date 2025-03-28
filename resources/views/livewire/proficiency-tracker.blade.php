<div>
    <div class="grid grid-cols-1 gap-8 py-12">
        @foreach ($student_proficiencies as $student_proficiency)
            <div class="grid grid-cols-1 gap-2 md:grid-cols-[minmax(10%,_20%)_1fr] lg:grid-cols-[80px_1fr_200px]">
                <div class="flex items-center size-18 px-4 py-2 rounded-lg text-3xl tracking-wider text-gray-800 dark:text-gray-200 border border-green-800 dark:border-green-200 bg-green-200/25 dark:bg-green-800/25">
                    {{ $student_proficiency->proficiency->level_code }}
                </div>

                <div>
                    <h3 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold">{{ $student_proficiency->proficiency->name }}</h3>

                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $student_proficiency->proficiency->description }}</span>
                </div>

                {{-- TODO: This should be based on the number of modules the student completed
                <div class="w-full bg-gray-300 rounded-full dark:bg-gray-600">
                    <div class="text-xs font-medium text-center p-0.5 leading-none rounded-full w-[65%] bg-green-600 dark:bg-green-600 text-gray-200 dark:text-gray-400">65%</div>
                </div>
                --}}

                <div class="grid grid-cols-1 items-center md:grid-cols-[minmax(10%,_21%)_1fr] md:col-span-2 lg:col-span-1 lg:block lg:items-start">
                    <h3 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold">Achieved on</h4>

                    <span class="text-sm text-gray-600 dark:text-gray-400 md:text-xl lg:text-sm">{{ Helpers::parse_time_to_user_timezone($student_proficiency->created_at)->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
