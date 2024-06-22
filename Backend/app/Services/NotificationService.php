<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AppointmentReminder;
use App\Notifications\NewDiscountNotification;
use Twilio\Rest\Client as TwilioClient;

class NotificationService
{
    protected $twilio;

    public function __construct(TwilioClient $twilio)
    {
        $this->twilio = $twilio;
    }
    public function sendNotification(User $user, $message, $type): void
    {
        switch ($user->subscription_type) {
            case 'sms':
                $this->sendSms($user->phone, $message);
                break;
            case 'telegram':
                $this->sendTelegram($user->telegram_chat_id, $message);
                break;
            case 'email':
            default:
                $user->notify(new $type($message));
                break;
        }
    }
    protected function sendSms($to, $message)
    {
        $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message,
        ]);
    }
    protected function sendTelegram($chatId, $message)
    {
        $telegramApiUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
        $data = ['chat_id' => $chatId, 'text' => $message,];
        $ch = curl_init($telegramApiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
