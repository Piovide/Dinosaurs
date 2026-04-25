@include('errors.layout', [
    'code' => 404,
    'title' => 'Pagina non trovata',
    'message' => 'La risorsa che stai cercando non esiste o potrebbe essere stata spostata.',
    'help' => 'Controlla l\'indirizzo oppure torna alla home per continuare la navigazione.',
])
