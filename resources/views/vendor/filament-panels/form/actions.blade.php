@props([
    'actions' => [],
    'fullWidth' => false,
    'alignment' => 'start',
])

<div @class([
    'filament-actions flex flex-wrap items-center gap-3',
    'sm:justify-start' => $alignment === 'start',
    'sm:justify-end' => $alignment === 'end',
    'sm:justify-center' => $alignment === 'center',
    'sm:justify-between' => $alignment === 'between',
])>
    {{ $slot }}
</div> 