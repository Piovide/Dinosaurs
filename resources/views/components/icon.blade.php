@props([
    'name',
    'size' => null,
    'color' => null,
    'padded' => false,
    'align' => 'middle',
    'path' => '/build/assets/svg/bootstrap-icons.svg'
])

<svg {{ $attributes->class([
        'icon',
        "icon-$size" => $size,
        "icon-$color" => $color,
        'icon-padded' => $padded,
        "align-$align" => $align
    ]) }}>
    <use href="{{ asset($path) }}#{{ $name }}"></use>
</svg>
