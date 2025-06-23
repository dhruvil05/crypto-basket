<?php

namespace App\Listeners;

use App\Events\WalletDepositRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\WalletRejectionMail;
use Illuminate\Support\Facades\Mail;

class SendWalletRejectionMail implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WalletDepositRejected $event): void
    {
        $user = $event->transaction->user;

        if (!$user) {
            \Log::error("User is null in SendWalletRejectionMail. Transaction ID: {$event->transaction->id}");
            return;
        }

        Mail::to($user->email)->send(new WalletRejectionMail($event->transaction));
    }
}
