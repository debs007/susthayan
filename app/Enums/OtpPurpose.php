<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case Login = 'login';
    case TwoFactor = 'two_factor';
    case PasswordReset = 'password_reset';
}
