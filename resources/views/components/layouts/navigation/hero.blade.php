<div class="hero overflow-hidden relative bg-white">
    <x-language-switcher />
    <div
        class="flex flex-col sm:flex-row justify-start items-center gap-8 sm:gap-16 lg:gap-32 mx-auto sm:pl-16 py-8 sm:py-16 max-w-7xl">
        <div class="absolute -left-56 -top-16 z-0 w-[40%] text-primary/15">
            <x-svg.psg-irodahazak-symbol />
        </div>
        <div class="logo relative z-10 pt-8 sm:pt-0">
            <a wire:navigate.hover href="{{ localized_route('home') }}" class="flex items-center">
                <img src="{{ Vite::asset('resources/images/psg-raktarak-logo.webp') }}" class="mr-3 h-12 sm:h-20"
                    alt="PSG Raktárak logo" loading="lazy">
            </a>
        </div>
        <div class="slogan flex flex-col items-center text-center">
            @if (request()->routeIs('home', 'en.home'))
                <h1 class="text-xl sm:text-2xl font-bold text-logogray/80 mb-3">
                    {{ __('Warehouses for rent in Budapest – the whole warehouse market in one place') }}</h1>
            @endif
            <p class="text-2xl sm:text-3xl">{!! __('page.hero.slogan') !!}</p>
        </div>
    </div>
</div>
