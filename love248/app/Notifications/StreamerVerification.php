<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class StreamerVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public string $document, public int $userId)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(Storage::url($this->document));
        
        return (new MailMessage())
                    ->replyTo($notifiable->email)
                    ->subject(__("Streamer Verification Request"))
                    ->line(__("New Streamer Identiy Verification Request from :name (:username)", [
                        'name' => $notifiable->name,
                        'username' => '@' . $notifiable->username
                    ]))
                    ->line(__("View the verification document using the link below:"))
                    ->action(__("View Document"), $url)
                    ->line(__("To approve this streamer, click the button below:"))
                    ->action(__("Approve Streamer"), route('admin.approveStreamer') . '?user=' . $this->userId);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
