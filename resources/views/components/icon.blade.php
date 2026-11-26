@props(['name', 'class' => ''])
<svg {{ $attributes->merge(['class' => trim('i '.$class)]) }} viewBox="0 0 24 24" aria-hidden="true">{!! \App\Support\Icons::path($name) !!}</svg>
