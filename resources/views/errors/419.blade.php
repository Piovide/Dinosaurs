@include('errors.layout', [
    'code' => 419,
    'title' => 'Sessione scaduta',
    'message' => 'La sessione e scaduta. Potrebbe essere necessario aggiornare la pagina e riprovare.',
    'help' => 'Per motivi di sicurezza alcuni form scadono automaticamente dopo un periodo di inattivita.',
])
