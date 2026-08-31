<?php

namespace App\Exceptions;

use RuntimeException;

class PrescriptionRequiredException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This order needs an approved prescription before checkout can continue.');
    }
}
