<?php

namespace App\Services;

use App\Notifications\SendWhatsappMessegeNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class SendWhatsappMessage
{
    public function __construct(private Authenticatable $model, private string $text, private string $template = "general")
    {
    }

    public function send(): bool
    {
        $this->model->notify(new SendWhatsappMessegeNotification($this->model->phone_number, $this->template, $this->text));

        return true;
    }
}