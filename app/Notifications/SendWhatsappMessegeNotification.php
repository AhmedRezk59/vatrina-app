<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use Illuminate\Notifications\Notification;

class SendWhatsappMessegeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $phoneNumber,public string $template,public string $text)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsapp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name($this->template) // Name of your configured template
            ->body(Component::text($this->text))
            ->body(Component::dateTime(new \DateTimeImmutable))
            ->to($this->phoneNumber);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}