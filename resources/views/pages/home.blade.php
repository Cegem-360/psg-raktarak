<div>

    <div class="relative">

        <div class="kategoria-valaszto grid grid-cols-1 md:grid-cols-3 font-bold">
            <a href="{{ localized_route('budapest.category', ['category' => 'kiado-belvarosi-irodak']) }}"
                class="hero-image relative group">
                <img src="{{ Vite::asset('resources/images/pesti_oldal.webp') }}" alt="Pesti oldal és agglomeráció"
                    class="w-full h-auto object-cover aspect-[3/2]" loading="lazy" />
                <div
                    class="flex items-center justify-center text-xl text-center text-white _bg-accentdark/90_ bg-primary backdrop-blur-xl h-[4.5rem] group-hover:bg-accent {{-- group-hover:h-24 --}} duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                    <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        {{ __('Pesti oldal és agglomeráció') }}</h2>
                </div>
            </a>
            <a href="{{ localized_route('budapest.category', ['category' => 'kiado-budai-irodak']) }}"
                class="hero-image relative group">
                <img src="{{ Vite::asset('resources/images/budai_oldal.webp') }}" alt="Budai oldal"
                    class="w-full h-auto object-cover aspect-[3/2]" loading="lazy" />
                <div
                    class="flex items-center justify-center text-xl text-center text-white _bg-accentdark/90_ bg-logogray backdrop-blur-xl h-[4.5rem] group-hover:bg-accent {{-- group-hover:h-24 --}} duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                    <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        {{ __('Budai oldal') }}</h2>
                </div>
            </a>
            <a href="{{ localized_route('budapest.category', ['category' => 'kiado-vaci-uti-irodak']) }}"
                class="hero-image relative group">
                <img src="{{ Vite::asset('resources/images/videki_raktarak.webp') }}" alt="Vidéki raktárak"
                    class="w-full h-auto object-cover aspect-[3/2]" loading="lazy" />
                <div
                    class="flex items-center justify-center text-xl text-center text-white _bg-accentdark/90_ bg-primary backdrop-blur-xl h-[4.5rem] group-hover:bg-accent {{-- group-hover:h-24 --}} duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                    <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        {{ __('Vidéki raktárak') }}</h2>
                </div>
            </a>
        </div>

    </div>
    <x-pages.sections.home.bizalomerosito />

    @include('pages.forms.szuro')

    <x-pages.sections.home.kiemelt-ajanlatok />

    {{-- <x-pages.sections.home.rolunk-mondtak /> --}}

    <x-pages.sections.home.referenciak />

    @if (app()->getLocale() === 'hu')
        <x-pages.sections.home.blog-section />

        <x-pages.sections.home.hirek-section />
    @endif

</div>
