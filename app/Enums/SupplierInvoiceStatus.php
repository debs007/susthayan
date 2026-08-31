<?php

namespace App\Enums;

enum SupplierInvoiceStatus: string
{
    case PendingMatch = 'pending_match';
    case Matched = 'matched';
    case Approved = 'approved';
    case Disputed = 'disputed';
    case Paid = 'paid';
}
