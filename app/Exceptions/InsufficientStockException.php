<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly int $productId, public readonly int $requested, public readonly int $available)
    {
        parent::__construct("Only {$available} unit(s) available for product #{$productId}, {$requested} requested.");
    }
}
