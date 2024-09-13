<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Test extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public function __construct(public ?string $emails = null) {}

    public function via(object $notifiable): array
    {
        return setNotificationChannels($notifiable, 'test');
    }

    public function toMail(): MailMessage
    {
        $mail = new MailMessage;
        $mail->subject('Oh2Bees: Test Email');
        $mail->view('emails.test');

        return $mail;
    }

    public function toDiscord(): string
    {
        $message = 'Oh2Bees: This is a test Discord notification from Oh2Bees.';
        $message .= "\n\n";
        $message .= '[Go to your dashboard]('.base_url().')';

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => 'Oh2Bees: This is a test Telegram notification from Oh2Bees.',
            'buttons' => [
                [
                    'text' => 'Go to your dashboard',
                    'url' => base_url(),
                ],
            ],
        ];
    }
}
