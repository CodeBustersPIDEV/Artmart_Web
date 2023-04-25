<?php
namespace App\Service;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;

class SmsService extends Client
{
    public function sendVerificationCode($phoneNumber, $verificationCode)
    {
        $basic = new Basic($this->apiKey, $this->apiSecret);
        $client = new Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($phoneNumber, 'MyApp', 'Your verification code is: '.$verificationCode)
        );

        return $response;
    }
}
