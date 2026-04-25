@include('errors.layout', [
    'code' => 503,
    'title' => 'Servizio temporaneamente non disponibile',
    'message' => 'Stiamo effettuando un aggiornamento o una manutenzione del sistema.',
    'help' => 'Riprova a breve: torneremo online il prima possibile.',
])
