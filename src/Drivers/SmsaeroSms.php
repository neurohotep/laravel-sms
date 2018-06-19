<?php

namespace Neurohotep\LaravelSms\Drivers;

class SmsaeroSms extends SmsDriver
{
    private $api_url, $sign;

    public function __construct($login, $password, $sign)
    {
        $this->api_url = "https://{$login}:{$password}@gate.smsaero.ru/v2/";
        $this->sign = $sign;
    }

    public function login()
    {
        if ($ch = curl_init($this->api_url . 'auth')) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            curl_close($ch);
            return $result;
        }
        return false;
    }

    public function send($phone = null, string $message = '', array $config = [])
    {
        if ($ch = curl_init($this->api_url . 'send')) {
            if (isset($config['sign']) && !empty($config['sign'])) {
                $sign = $config['sign'];
            } else {
                $sign = $this->sign;
            }

            if (isset($config['channel']) && $this->correctChannel($config['channel'])) {
                $channel = $config['channel'];
            } else {
                $channel = 'INFO';
            }

            $options = [
                'sign' => $sign,
                'text' => $message,
                'channel' => $channel
            ];

            if (\is_array($phone)) {
                $options['numbers'] = $phone;
            } else {
                $options['number'] = $phone;
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options));
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            curl_close($ch);
            return $result;
        }
        return false;
    }

    private function correctChannel($channel = '')
    {
        return \in_array($channel, ['INFO', 'DIGITAL', 'INTERNATIONAL', 'DIRECT', 'SERVICE']) ? true : false;
    }
}