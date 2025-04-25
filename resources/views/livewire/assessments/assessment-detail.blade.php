<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assessment') }}: {{ $current_assessment['title'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- TODO: Add a condition to show the assessment questions only if the student has not taken them yet --}}
        {{-- I think the best option without having to create a pivot table of assessments and students it to based it from the --}}
        {{-- assessments_students_answers table, get the student_id and assessment_question_id->assessment->id == ID of this ASSESSMENT --}}
        <div
            x-data="{
                currentIndex: 0,
                currentQuestionNumber: 1,
                inIntro: true,
                isError: false,
                isFinalQuestion: false,
                isTransitioning: false,
                questionsArr: $wire.entangle('current_assessment_questions'),
                showResults: false,
                studentAnswerCurrentQuestion: [],
                studentAnswers: [],
                submitAnswer() {
                    if (this.studentAnswerCurrentQuestion.length == 0) {
                        this.isError = true

                        return
                    } else {
                        this.isError = false
                        this.studentAnswers[this.currentQuestionNumber] = []
                        this.isTransitioning = true

                        if (Array.isArray(this.studentAnswerCurrentQuestion)) {
                            this.studentAnswerCurrentQuestion.forEach(answer => this.studentAnswers[this.currentQuestionNumber].push(answer))
                        } else {
                            this.studentAnswers[this.currentQuestionNumber].push(this.studentAnswerCurrentQuestion)
                        }

                        setTimeout(() => {
                            if (this.questionsArr.length == this.currentQuestionNumber) {
                                this.isFinalQuestion = true // Need this so that it won't reload the last question before showing the results screen
                                $dispatch('validating-answers', { student_answers: this.studentAnswers })
                            } else {
                                this.currentQuestionNumber++
                                this.currentIndex++
                            }

                            this.studentAnswerCurrentQuestion = [] // Reset for next question
                            this.isTransitioning = false
                        }, 750) // Timeout duration for transition
                    }
                },
            }"
            x-init="$wire.on('showed-assessment-results', () => showResults = true)"
            class="max-w-7xl mx-auto sm:px-6 lg:px-8"
        >
            <div x-show="!showResults">
                <div
                    x-show="inIntro"
                    x-transition:leave="transition transform duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 -translate-x-50"
                    class="grid grid-cols-1 lg:grid-cols-2 mb-10"
                >
                    <div class="from-green-400 to-gray-100 dark:to-gray-900/20 rounded-t-3xl lg:rounded-tl-3xl bg-gradient-to-tl lg:bg-gradient-to-b">
                        <img src="{{ asset('images/bg-assessment.webp') }}" class="h-full w-full object-cover ml-0 mt-5 lg:ml-5 lg:mt-0" alt="Assessment Image" title="Assessment Image" loading="lazy" />
                    </div>

                    <div class="flex flex-col gap-10 bg-gray-300 dark:bg-gray-600 py-12 px-16 lg:py-6 lg:px-10">
                        <h3 class="text-xl lg:text-3xl text-gray-800 dark:text-gray-200">Welcome to the assessment!</h3>

                        <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300">You are about to take the assessment <span class="font-semibold">{{ $current_assessment['title'] }}</span>. This assessment has <span x-text="`${questionsArr.length} ${questionsArr.length > 1 ? 'questions' : 'question'}`" class="font-semibold"></span> for you to answer.</p>

                        <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300">Assessments like these are a great way to hone your English skills by answering a series of questions that will test your understanding of the English language.</p>

                        <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300">Take a deep breath, relax, and goodluck! To start the assessment, click the button below.</p>

                        <x-button x-on:click="inIntro = !inIntro" class="w-fit mx-auto lg:mx-0">Start Now</x-button>
                    </div>
                </div>

                <div
                    x-show="!isTransitioning && !inIntro && !isFinalQuestion"
                    x-cloak
                    x-transition:enter="transition ease-out delay-500 duration-1000"
                    x-transition:enter-start="opacity-0 -translate-x-25"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition transform duration-750"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="rounded-xl p-4 shadow-md bg-gray-300 dark:bg-gray-600"
                >
                    <p class="text-gray-800 dark:text-gray-200 font-semibold text-2xl"><span x-text="currentQuestionNumber"></span>. <span x-text="questionsArr[currentIndex].question"></span></p>

                    <div class="space-y-4 p-2">
                        <div x-show="questionsArr[currentIndex].no_of_correct_answers > 1">
                            <p class="text-gray-600 dark:text-gray-300">Select up to <span x-text="questionsArr[currentIndex].no_of_correct_answers"></span> choices.</p>
                        </div>

                        <div x-show="isError" class="text-sm text-red-600 dark:text-red-400">You must select an answer!</div>

                        <template x-for="c in questionsArr[currentIndex].choices" :key="c.choice">
                            <div class="flex items-center gap-2 mt-2">
                                <template x-if="questionsArr[currentIndex].no_of_correct_answers > 1">
                                    <input
                                        x-bind:disabled="studentAnswerCurrentQuestion.length >= questionsArr[currentIndex].no_of_correct_answers && !studentAnswerCurrentQuestion.includes(c.choice)"
                                        x-bind:value="c.choice"
                                        x-model="studentAnswerCurrentQuestion"
                                        class="mb-0"
                                        type="checkbox"
                                    />
                                </template>

                                <template x-if="questionsArr[currentIndex].no_of_correct_answers == 1">
                                    <input
                                        x-bind:value="c.choice"
                                        x-model="studentAnswerCurrentQuestion"
                                        class="mb-0"
                                        type="radio"
                                    />
                                </template>

                                <label x-text="c.choice" class="text-gray-800 dark:text-gray-200"></label>
                            </div>
                        </template>

                        <x-secondary-button x-on:click="submitAnswer" class="mt-6">Submit Answer</x-secondary-button>
                    </div>
                </div>
            </div>

            <div
                x-show="showResults"
                x-cloak
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-50"
                x-transition:enter-end="opacity-100 scale-100"
                class="flex flex-col items-center justify-center mt-12"
            >
                <div class="max-w-2xl space-y-8 w-[75%] lg:w-full">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600/60 to-green-600/60 dark:from-blue-500 dark:to-green-500 p-8 text-center shadow-lg shadow-green-300">
                        <h2 class="text-5xl font-bold text-gray-200 drop-shadow-lg">{{ $correct_answers_count }} / {{ $correct_answers_of_assessment_count }}</h2>
                        <p class="mt-3 text-lg font-medium text-gray-600 dark:text-gray-300">({{ $score_percentage }}%)</p>

                        {{-- Show congratulatory "confetti" if student answered all questions --}}
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
                                x-data="{ isOpen: false }"
                                x-bind:class="{'rounded-lg': !isOpen, 'rounded-t-lg': isOpen}"
                                class="border bg-gray-200 dark:bg-gray-800 shadow-sm"
                            >
                                <div
                                    x-bind:class="{'rounded-b-0 rounded-t-lg bg-green-200 dark:bg-green-800': isOpen, 'rounded-lg': !isOpen}"
                                    x-on:click="isOpen = !isOpen"
                                    class="transition duration-300 flex justify-between gap-2 items-center p-3 cursor-pointer hover:bg-green-300 dark:hover:bg-green-600"
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
                                                    'font-bold text-green-600': c.is_correct == 1,
                                                    'text-red-500': studentAnswers[idx + 1]?.includes(c.choice) && c.is_correct == 0
                                                }"
                                                x-text="c.choice"
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
        </div>
    </div>
</div>
