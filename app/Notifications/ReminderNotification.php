<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Http;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reminder;

    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Reminder Alert',
            'message' => 'It’s time to take your supplement!',
            'supplement_id' => $this->reminder->supplement_id,
            'time' => $this->reminder->time,
        ];
    }

    public function toFcm($notifiable)
    {
        $deviceToken = $notifiable->device_token; // Ensure users have this stored in the database

        $response = Http::withHeaders([
            'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $deviceToken,
            'notification' => [
                'title' => 'Reminder Alert',
                'body' => 'It’s time to take your supplement!',
                'sound' => 'default',
            ],
            'data' => [
                'supplement_id' => $this->reminder->supplement_id,
                'time' => $this->reminder->time,
            ]
        ]);

        return $response->json();
    }
}

