<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Assessments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (count($assessments) > 0)
                @foreach ($assessments as $index => $assessment)
                    <div class="mb-4 mt-2 shadow-lg shadow-gray-300 dark:shadow-gray-600 first:mt-4">
                        <x-accordion-toggle :content_classes="'bg-gray-100 dark:bg-gray-800'" :header_classes="'pr-3'" :index="$index" :is_opened="'true'" :parent_classes="'bg-gray-300 dark:bg-gray-600'">
                            <x-slot name="title">
                                <div class="flex justify-between gap-3 p-2 sm:p-4">
                                    <div class="flex flex-1 flex-col">
                                        <h2 class="text-gray-800 dark:text-gray-200 font-bold">{{ $assessment['title'] }}</h2>
                                    </div>
                                </div>
                            </x-slot>

                            <x-slot name="content">
                                <div class="p-2 sm:p-4">
                                    {{-- :key param in Livewire components is used to uniquely identify each state, especially for paginated content like this --}}
                                    <livewire:assessments.attempt-history :key="$assessment['id']" :assessment_id="$assessment['id']" />
                                </div>
                            </x-slot>
                        </x-accordion-toggle>
                    </div>
                @endforeach

                {{ $assessments->withQueryString()->links() }}
            @else
                <p class="text-gray-800 dark:text-gray-200">No assessments found. You need to take an assessment for them to show up here.</p>
            @endif
        </div>
    </div>
</div>
