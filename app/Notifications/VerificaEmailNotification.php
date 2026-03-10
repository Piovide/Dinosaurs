<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerificaEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica il tuo indirizzo email – ' . config('app.name'))
            ->greeting('Ciao!')
            ->line('Grazie per esserti registrato su **' . config('app.name') . '**.')
            ->line('Clicca sul pulsante qui sotto per verificare il tuo indirizzo email e attivare l\'account.')
            ->action('Verifica Email', $url)
            ->line('Il link scadrà tra 60 minuti.')
            ->line('Se non hai creato un account, puoi ignorare questa email.')
            ->salutation('A presto, il team di ' . config('app.name'));
    }
}
