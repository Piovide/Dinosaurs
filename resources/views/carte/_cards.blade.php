@foreach ($carte as $carta)
    <x-carta-card :carta="$carta" :combos="$userCombosByCarta[$carta->id_carta] ?? null" />
@endforeach
