{{--
  Componente universale per mostrare l'icona di una rarità o tipologia.
  Props:
    $record  — istanza di CollezioneRarita o CollezioneTipologia
    $size    — altezza dell'icona (default '20px')
    $class   — classi aggiuntive
--}}
@props(['record', 'size' => '20px', 'class' => ''])

@if($record && $record->icona)
    @if($record->tipo_icona === 'bootstrap')
        <x-icon name="{{ $record->icona }}"
                {{ $attributes->class(['icona-badge', $class]) }}
                style="width:{{ $size }};height:{{ $size }};vertical-align:middle;" />
    @else
        <img src="{{ $record->icona_url }}"
             alt="{{ $record->nome }}"
             {{ $attributes->class(['icona-badge', $class]) }}
             style="height:{{ $size }};width:auto;vertical-align:middle;object-fit:contain;">
    @endif
@endif
