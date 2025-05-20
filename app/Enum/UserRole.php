<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin        = 'admin';
    case Provider     = 'provider';
    case Patient      = 'patient';
    case Biller       = 'biller';
    case Scheduler    = 'scheduler';
    case TH           = 'training_and_hiring';
    case OPD          = 'operational_director';
}
