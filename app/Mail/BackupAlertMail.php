<?php

namespace App\Mail;

use App\Models\BackupAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?BackupAlert $alert;
    public bool $isTest;

    public function __construct(?BackupAlert $alert = null, bool $isTest = false)
    {
        $this->alert = $alert;
        $this->isTest = $isTest;
    }

    public function build()
    {
        $subject = $this->isTest
            ? '[BMS] Test Email Notification'
            : '[BMS] ' . ($this->alert?->title ?? 'Backup Alert');

        return $this
            ->subject($subject)
            ->view('emails.backup-alert');
    }
}