<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\GallerySales;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGallerySale extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $sale;
    public function __construct(GallerySales $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'is_streamer' => $this->sale->user->is_streamer,
            'username' => $this->sale->user->username,
            'profile_picture' => $this->sale->user->profile_picture,
            'gallery' => $this->sale->gallery,
            'price' => $this->sale->price
        ];
    }
}
