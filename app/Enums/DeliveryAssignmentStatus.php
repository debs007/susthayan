<?php

namespace App\Enums;

enum DeliveryAssignmentStatus: string
{
    case Assigned = 'assigned';
    case PickedUp = 'picked_up';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Failed = 'failed';
}
