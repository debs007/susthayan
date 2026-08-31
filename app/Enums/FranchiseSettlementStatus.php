<?php

namespace App\Enums;

enum FranchiseSettlementStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Paid = 'paid';
}
