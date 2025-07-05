<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionPdfMail extends Notification
{
    use Queueable;

    protected $pdfFilePath;
    protected $fileName;

    public function __construct($pdfFilePath, $fileName)
    {
        $this->pdfFilePath = $pdfFilePath;
        $this->fileName = $fileName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Transaction Report')
            ->line('Please find the attached transaction report.')
            ->attach($this->pdfFilePath, [
                'as' => $this->fileName,
                'mime' => 'application/pdf',
            ]);
    }

    public function toArray($notifiable)
    {
        return [];
    }
}

?>