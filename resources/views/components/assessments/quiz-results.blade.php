@props([
    'correct_answers_count',
    'correct_answers_of_assessment_count',
    'questions_arr' => '[]', // Pass as JSON-encoded string from PHP
    'score_percentage',
    'student_answers' => '[]' // Pass as JSON-encoded string from PHP
])

<div
    x-data="{
        questionsArr: @js($questions_arr),
        studentAnswers: @js($student_answers)
    }"
    x-cloak
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 scale-50"
    x-transition:enter-end="opacity-100 scale-100"
    class="flex flex-col items-center justify-center mt-12"
>
    <div class="max-w-2xl space-y-8 w-[60%] md:w-[75%] lg:w-full">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600/60 to-green-600/60 dark:from-blue-500 dark:to-green-500 p-8 text-center shadow-lg shadow-green-300">
            <h2 class="text-5xl font-bold text-gray-200 drop-shadow-lg">{{ $correct_answers_count }} / {{ $correct_answers_of_assessment_count }}</h2>
            <p class="mt-3 text-lg font-medium text-gray-600 dark:text-gray-300">({{ $score_percentage }}%)</p>

            @if ($score_percentage == '100')
                <div class="absolute inset-0 overflow-hidden">
                    <template x-for="confettiIndex in 18" :key="confettiIndex">
                        <span
                            class="absolute text-2xl animate-confetti-fall -top-[3em]"
                            x-bind:style="`
                                left: ${Math.random() * 100}%;
                                animation-delay: ${Math.random() * 2}s;
                            `"
                            x-text="['🎉', '🎊', '✨', '💥', '🔥', '🎈'][Math.floor(Math.random() * 6)]"
                        ></span>
                    </template>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3">
            <h3 class="text-gray-800 dark:text-gray-200 text-2xl lg:text-4xl">Results</h3>
            <hr class="h-1 bg-gray-800 dark:bg-gray-200 w-[75%] lg:w-full">
        </div>

        <div class="space-y-4">
            <template x-for="(question, idx) in questionsArr" :key="idx">
                <div
                    x-data="{
                        isOpen: false,
                        hasAllCorrectAnswers() {
                            const correctChoices = question.choices.filter(c => c.is_correct == 1).map(c => c.choice)
                            const selectedChoices = studentAnswers[idx] || []

                            if (correctChoices.length !== selectedChoices.length) return false

                            return correctChoices.every(choice => selectedChoices.includes(choice))
                        }
                    }"
                    x-bind:class="{'rounded-lg': !isOpen, 'rounded-t-lg': isOpen}"
                    class="border bg-gray-200 dark:bg-gray-800 shadow-sm"
                >
                    <div
                        x-bind:class="{
                            'rounded-b-0 rounded-t-lg': isOpen,
                            'rounded-lg': !isOpen,
                            'bg-green-200 dark:bg-green-800': hasAllCorrectAnswers(),
                            'bg-red-200 dark:bg-red-800': !hasAllCorrectAnswers()
                        }"
                        x-on:click="isOpen = !isOpen"
                        class="flex justify-between gap-2 items-center p-3 cursor-pointer"
                    >
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-md">
                            <span x-text="`${idx + 1}. ${question.question}`"></span>
                        </h3>

                        <svg
                            x-bind:class="{ 'rotate-180': isOpen }"
                            class="size-6 transition duration-300 ease-in-out text-gray-800 dark:text-gray-200"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <div
                        x-show="isOpen"
                        x-collapse
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition transform duration-500"
                        x-transition:leave-start="h-full opacity-100"
                        x-transition:leave-end="h-0 opacity-0 -translate-x-50"
                        class="text-sm text-gray-800 dark:text-gray-200"
                    >
                        <ul class="px-6 py-4 list-disc list-inside">
                            <div x-show="question.no_of_correct_answers > 1" class="mb-2">
                                <p class="text-gray-600 dark:text-gray-300">This question has <span x-text="question.no_of_correct_answers"></span> answers.</p>
                            </div>

                            <template x-for="(c, index) in question.choices" :key="index">
                                <li
                                    x-bind:class="{
                                        'font-bold text-green-500 dark:text-green-600': c.is_correct == 1,
                                        'text-red-400 dark:text-red-600': studentAnswers[idx]?.includes(c.choice) && c.is_correct == 0,
                                        'bg-gray-600': studentAnswers[idx]?.includes(c.choice)
                                    }"
                                    x-text="c.choice"
                                    class="my-1 pl-1 py-1"
                                >
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
