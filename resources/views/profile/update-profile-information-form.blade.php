<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                    "
                    type="file"
                    id="photo"
                    class="hidden"
                />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div x-show="!photoPreview" class="mt-2">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span x-bind:style="'background-image: url(\'' + photoPreview + '\');'" class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"></span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <div class="col-span-6">
            <x-label value="{{ __('First Name') }}" is_required="true" for="first_name" />

            <x-input wire:model="state.first_name" class="mt-1 block w-full" type="text" id="first_name" autocomplete="first_name" />

            <x-input-error class="mt-2" for="first_name" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Middle Name') }}" for="middle_name" />

            <x-input wire:model="state.middle_name" class="mt-1 block w-full" type="text" id="middle_name" autocomplete="middle_name" />

            <x-input-error class="mt-2" for="middle_name" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Last Name') }}" is_required="true" for="last_name" />

            <x-input wire:model="state.last_name" class="mt-1 block w-full" type="text" id="last_name" autocomplete="last_name" />

            <x-input-error class="mt-2" for="last_name" />
        </div>

        <div class="col-span-6">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && !$this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Date of Birth') }}" is_required="true" for="date_of_birth" />

            <x-input wire:model="state.date_of_birth" class="mt-1 block w-full" type="date" id="date_of_birth" autocomplete="date_of_birth" />
            <small class="text-slate-700 dark:text-slate-300">Format: DD/MM/YYYY</small>

            <x-input-error class="mt-2" for="date_of_birth" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Gender') }}" is_required="true" for="gender" />

            <x-select wire:model="state.gender" :inline_block="false">
                @foreach (\App\Enums\Genders::cases() as $gender)
                    <option value="{{ $gender->value }}">{{ $gender->value }}</option>
                @endforeach
            </x-select>

            <x-input-error class="mt-2" for="gender" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
