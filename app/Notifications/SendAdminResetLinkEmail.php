<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAdminResetLinkEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $token)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('بريد إعادة تعيين كلمة السر')
            ->line('لقد أرسل هئا البريد إليك لأنك طلبت إعادة تعيين ةكلمة السر.')
            ->action('إعادة تعيين كلمة السر', config('app.frontend_url') . (route('admin.password.store', ['token' => $this->token], false)))
            ->line('هذا الرابط لن يعمل بعد 60 دقيقة.')
            ->line('إذا لم تطلب إعادة تعيين كلمة السر فلا حاجة للقيام بأى شئ.');
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