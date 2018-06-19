<?php

namespace Neurohotep\LaravelSms\Drivers;

interface SmsContract
{
    public function send($phone = null, string $message = '', array $config = []);
}