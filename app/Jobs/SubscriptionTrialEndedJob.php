<?php

namespace App\Jobs;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscriptionTrialEndedJob implements ShouldBeEncrypted, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Team $team
    ) {}

    public function handle(): void
    {
        try {
            $session = getStripeCustomerPortalSession($this->team);
            $mail = new MailMessage;
            $mail->subject('Action required: You trial in Oh2Bees Cloud ended.');
            $mail->view('emails.trial-ended', [
                'stripeCustomerPortal' => $session->url,
            ]);
            $this->team->members()->each(function ($member) use ($mail) {
                if ($member->isAdmin()) {
                    ray('Sending trial ended email to '.$member->email);
                    send_user_an_email($mail, $member->email);
                    send_internal_notification('Trial reminder email sent to '.$member->email);
                }
            });
        } catch (\Throwable $e) {
            send_internal_notification('SubscriptionTrialEndsSoonJob failed with: '.$e->getMessage());
            ray($e->getMessage());
            throw $e;
        }
    }
}
