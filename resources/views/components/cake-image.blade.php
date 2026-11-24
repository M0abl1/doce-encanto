@props(['cake'])
@if ($cake->imageUrl())
    <img src="{{ $cake->imageUrl() }}" alt="{{ $cake->name }}" loading="lazy" {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>
@else
    <x-cake-art :color="$cake->color" :kind="$cake->artKind()" :title="$cake->name" {{ $attributes }} />
@endif
