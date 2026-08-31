<?php

namespace App\Enums;

enum SupplierPaymentStatus: string
{
    case Initiated = 'initiated';
    case Completed = 'completed';
    case Failed = 'failed';
}
