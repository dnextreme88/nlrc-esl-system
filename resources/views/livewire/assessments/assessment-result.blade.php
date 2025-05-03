<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assessment') }}: {{ $current_assessment->assessment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div
            x-data="{
                questionsArr: $wire.entangle('current_assessment_questions'),
                studentAnswers: $wire.entangle('student_answers')
            }"
            x-cloak
        >
            <x-assessments.quiz-results
                :correct_answers_count="$correct_answers_count"
                :correct_answers_of_assessment_count="$correct_answers_of_assessment_count"
                :questions_arr="$current_assessment_questions"
                :score_percentage="$score_percentage"
                :student_answers="$student_answers"
            />
        </div>
    </div>
</div>
