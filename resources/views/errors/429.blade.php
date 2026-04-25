@include('errors.layout', [
    'code' => 429,
    'title' => 'Troppe richieste',
    'message' => 'Hai inviato troppe richieste in poco tempo. Attendi qualche istante prima di riprovare.',
    'help' => 'Questo limite protegge il sito e garantisce prestazioni stabili per tutti.',
])
