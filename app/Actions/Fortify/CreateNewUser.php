<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'min:2', 'max:96'],
            'middle_name' => ['sometimes', 'max:96'],
            'last_name' => ['required', 'string', 'min:2', 'max:96'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:128', 'unique:'.User::class],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'gender' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'role_id' => Role::isStudent()->first()->id,
            'first_name' => $input['first_name'],
            'middle_name' => $input['middle_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'date_of_birth' => $input['date_of_birth'],
            'gender' => $input['gender'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
