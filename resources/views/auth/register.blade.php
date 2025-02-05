<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-application-logo :link="'home'" class="size-16" />
        </x-slot>

        <p class="mt-6 text-gray-600 dark:text-gray-400">All fields marked with <x-red-asterisk /> are required.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mt-4">
                <x-label for="first_name" is_required="true" value="{{ __('First Name') }}" />
                <x-input wire:model="first_name" id="first_name" class="block mt-1 w-full" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="first_name" maxlength="96" />

                <x-input-error class="mt-2" for="first_name" />
            </div>

            <div class="mt-4">
                <x-label for="middle_name" value="{{ __('Middle Name') }}" />
                <x-input wire:model="middle_name" id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" value="{{ old('middle_name') }}" autofocus autocomplete="middle_name" maxlength="96" />

                <x-input-error class="mt-2" for="middle_name" />
            </div>

            <div class="mt-4">
                <x-label for="last_name" is_required="true" value="{{ __('Last Name') }}" />
                <x-input wire:model="last_name" id="last_name" class="block mt-1 w-full" type="text" name="last_name" value="{{ old('last_name') }}" required autofocus autocomplete="last_name" maxlength="96" />

                <x-input-error class="mt-2" for="last_name" />
            </div>

            <div class="mt-4">
                <x-label for="email" is_required="true" value="{{ __('Email') }}" />
                <x-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />

                <x-input-error class="mt-2" for="email" />
            </div>

            <div class="mt-4">
                <x-label for="date_of_birth" is_required="true" value="{{ __('Date of Birth') }}" />
                <x-input wire:model="date_of_birth" id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="date_of_birth" />
                <small class="text-slate-700 dark:text-slate-300">Format: DD/MM/YYYY</small>

                <x-input-error class="mt-2" for="date_of_birth" />
            </div>

            <div class="mt-4">
                <x-label for="gender" is_required="true" value="{{ __('Gender') }}" />
                <x-select wire:model="gender" name="gender">
                    @foreach (\App\Enums\Genders::cases() as $gender)
                        <option value="{{ $gender->value }}">{{ $gender->value }}</option>
                    @endforeach
                </x-select>

                <x-input-error class="mt-2" for="gender" />
            </div>

            <div class="mt-4">
                <x-label is_required="true" for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <small class="text-slate-700 dark:text-slate-300">Your password should be at least 8 characters long</small>

                <x-input-error class="mt-2" for="password" />
            </div>

            <div class="mt-4">
                <x-label is_required="true" for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>
