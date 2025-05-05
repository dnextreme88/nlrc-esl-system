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
        <div
            x-data="{
                currentIndex: 0,
                currentQuestionNumber: 1,
                inIntro: true,
                isError: false,
                isFinalQuestion: false,
                isPassed: $wire.entangle('is_assessment_passed'),
                isTransitioning: false,
                questionsArr: $wire.entangle('current_assessment_questions'),
                showResults: false,
                studentAnswerCurrentQuestion: [],
                studentAnswers: $wire.entangle('student_answers'),
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
                    x-show="inIntro && !isPassed"
                    x-cloak
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

                    <div class="p-4 bg-gray-200 dark:bg-gray-600 shadow-md shadow-gray-500 mt-5 lg:mt-10 col-span-1 lg:col-span-2">
                        <livewire:assessments.attempt-history :assessment_id="$current_assessment['id']" />
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

            <div x-show="showResults || isPassed" x-cloak>
                @if ($assessment_uuid)
                    <livewire:assessments.assessment-result :assessment_uuid="$assessment_uuid" />
                @endif
            </div>
        </div>
    </div>
</div>
