<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'Admin';
    case HEAD_TEACHER = 'Head Teacher';
    case STUDENT = 'Student';
    case TEACHER = 'Teacher';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::HEAD_TEACHER => 'Head Teacher',
            self::STUDENT => 'Student',
            self::TEACHER => 'Teacher',
        };
    }
}
