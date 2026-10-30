{{--
    The standard content page frame: city photo background with a white fade, a
    centred container and, optionally, the page heading.

    <x-page-section>
        <x-slot:heading>{{ __('About Us') }}</x-slot:heading>
        ...
    </x-page-section>

    `spacing` sets the vertical gap of the container (default space-y-3) and
    `heading-margin` the gap below the heading (default mb-16).
--}}
@props(['spacing' => 'space-y-3', 'headingMargin' => 'mb-16'])

<div {{ $attributes->class('relative bg-cover bg-center bg-no-repeat bg-fixed') }}
    style="background-image: url({{ Vite::asset('resources/images/cargo-loading-dock-doors-of-big-warehouse-building-2024-10-31-00-59-14-utc.webp') }});">
    <div class="absolute inset-0 z-1 bg-linear-to-b from-white/90 to-white/70"></div>
    <div class="relative z-10 container mx-auto {{ $spacing }} pt-24 pb-20">
        @isset($heading)
            <h1 class="mt-4 {{ $headingMargin }} font-bold text-5xl text-center drop-shadow text-logogray/80">
                {{ $heading }}</h1>
        @endisset

        {{ $slot }}
    </div>
</div>
