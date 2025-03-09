<div>
    <x-slot name="nav_menu">
        @auth
            <livewire:NavMenu />
        @else
            <nav class="flex justify-between items-center gap-3 p-4">
                <div>
                    <x-application-logo class="h-9" />
                </div>

                <div>
                    <a
                        href="{{ route('login') }}"
                        class="text-gray-900 dark:text-gray-100 hover:text-green-600 dark:hover:text-green-300 rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-hidden dark:focus-visible:ring-white"
                    >
                        Log in
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="text-gray-900 dark:text-gray-100 hover:text-green-600 dark:hover:text-green-300 rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-hidden dark:focus-visible:ring-white"
                    >
                        Register
                    </a>
                </div>
            </nav>
        @endauth
    </x-slot>

    <section class="h-screen px-6 bg-linear-to-tr from-gray-100 to-green-300 pt-36 pb-80 dark:from-green-600 dark:bg-linear-to-br dark:to-gray-300 sm:py-32 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-5xl font-semibold tracking-tight text-gray-800 dark:text-gray-100 sm:text-7xl" aria-label="Welcome header">Where Learning Meets Excellence.</h2>

            <p class="mt-8 text-lg font-medium text-gray-600 text-pretty dark:text-gray-100 sm:text-xl/8" aria-label="Welcome description">NLRC-ESL will help you improve your English skills both written and verbally! Learn English quicker and faster with our talented teachers ready to guide your path.</p>
        </div>
    </section>
</div>
