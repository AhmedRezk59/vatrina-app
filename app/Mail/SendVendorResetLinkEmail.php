<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendVendorResetLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private $token)
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'بريد إعاد تعيين كلمة المرور',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return (new MailMessage)
            ->subject('بريد إعادة تعيين كلمة السر')
            ->line('لقد أرسل هئا البريد إليك لأنك طلبت إعادة تعيين ةكلمة السر.')
            ->action('إعادة تعيين كلمة السر', url(route('vendor.password.store', $this->token, false)))
            ->line('هذا الرابط لن يعمل بعد 60 دقيقة.')
            ->line('إذا لم تطلب إعادة تعيين كلمة السر فلا حاجة للقيام بأى شئ.');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
