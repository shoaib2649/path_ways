<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin    = 'admin';
    case Provider = 'provider';
    case Patient  = 'patient';
}
