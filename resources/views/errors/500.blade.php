@include('errors.layout', [
    'code' => 500,
    'title' => 'Errore interno del server',
    'message' => 'Si e verificato un problema imprevisto durante l\'elaborazione della richiesta.',
    'help' => 'Abbiamo registrato l\'anomalia. Riprova tra qualche minuto.',
])
