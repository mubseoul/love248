<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDetectionNotification extends Notification
{
    use Queueable;
    protected $userEmail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userEmail)
    {
        $this->userEmail = $userEmail;
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
        return (new MailMessage())
            ->subject(__("New Image Detection by a User"))
            ->line(__("New Image Detection Completed by the user: " . $this->userEmail))
            ->line(__("This user has been blocked from using this service."))
            ->action(__("Go to Admin Panel"), url('/admin/login'));
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
