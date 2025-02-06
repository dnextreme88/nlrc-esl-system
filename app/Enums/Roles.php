<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'Admin';
    case HEAD_TEACHER = 'Head Teacher';
    case STUDENT = 'Student';
    case TEACHER = 'Teacher';
}
