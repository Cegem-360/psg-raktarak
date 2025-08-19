<x-layouts.app>
    <x-slot name="title">{{ __('Impresszum') }} | PSG-RAKTÁRAK</x-slot>
    <x-slot name="meta">
        <meta name="robots" content="index, follow">
        <meta name="googlebot" content="index, follow">
        <meta name="description" content="PSG-RAKTÁRAK impresszum - {{ __('Jogi információk és elérhetőségek.') }}">
        <meta name="keywords" content="impresszum, {{ __('jogi információk') }}, PSG-RAKTÁRAK">
        <link rel="canonical" href="{{ Request::url() }}">
    </x-slot>

    <div class="relative bg-fixed bg-center bg-no-repeat bg-cover"
        style="background-image: url({{ Vite::asset('resources/images/view-of-london-city-united-kingdom-2025-02-19-07-53-44-utc.webp') }});">
        <div class="absolute inset-0 z-1 bg-gradient-to-b from-white/90 to-white/70"></div>
        <div class="container relative z-10 pt-24 pb-20 mx-auto space-y-8">
            <h2 class="mt-4 mb-16 text-5xl font-bold text-center drop-shadow text-logogray/80">
                {{ __('Impresszum') }}
            </h2>

            <div class="max-w-screen-xl p-8 mx-auto border shadow-xl backdrop-blur-3xl rounded-xl border-white/15">
                <div class="p-8 bg-white rounded-lg">
                    @if ($impresszum && $impresszum->content)
                        <div class="prose prose-lg text-gray-700 max-w-none">
                            {!! $impresszum->content !!}
                        </div>
                    @else
                        <div class="text-gray-600">
                            <p>{{ __('Az impresszum tartalma még nem lett beállítva.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
