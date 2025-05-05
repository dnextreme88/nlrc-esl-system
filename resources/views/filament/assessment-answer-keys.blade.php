<h2 class="text-xl">Question: <span class="font-bold">{{ $assessment_question->question }}</span></h2>

<ul class="space-y-2">
    @forelse ($assessment_choices as $choice)
        <li class="px-4 py-2 rounded-md {{ $choice->is_correct ? 'bg-green-300 dark:bg-green-600' : '' }}">{{ $choice->choice }}</li>
    @empty
        <li class="text-sm text-gray-800 dark:text-gray-200">No choices found for this question.</li>
    @endforelse
</ul>
