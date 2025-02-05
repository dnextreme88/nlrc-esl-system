<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'first_name' => ['required', 'string', 'min:2', 'max:96'],
            'middle_name' => ['sometimes', 'max:96'],
            'last_name' => ['required', 'string', 'min:2', 'max:96'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:128', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'gender' => ['required'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'first_name' => $input['first_name'],
                'middle_name' => $input['middle_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'date_of_birth' => $input['date_of_birth'],
                'gender' => $input['gender'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'first_name' => $input['first_name'],
            'middle_name' => $input['middle_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'date_of_birth' => $input['date_of_birth'],
            'gender' => $input['gender'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
