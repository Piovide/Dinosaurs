<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ReimpostaPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Reimposta la tua password – ' . config('app.name'))
            ->greeting('Ciao!')
            ->line('Hai richiesto di reimpostare la password del tuo account su **' . config('app.name') . '**.')
            ->line('Clicca sul pulsante qui sotto per scegliere una nuova password.')
            ->action('Reimposta Password', $url)
            ->line('Il link scadrà tra ' . config('auth.passwords.users.expire') . ' minuti.')
            ->line('Se non hai richiesto il reset della password, puoi ignorare questa email.')
            ->salutation('A presto, il team di ' . config('app.name'));
    }
}
