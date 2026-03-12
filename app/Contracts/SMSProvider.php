<?php

namespace App\Contracts;

interface SMSProvider
{
    public function send(string $mobile, string $otp): bool;
}
