<x-slot name="title">{{ __('PSG-RAKTÁRAK | Warehouses for rent, warehouses for sale, serviced instant warehouse solutions in Budapest. Tenant representation!') }}</x-slot>
<x-slot name="metaDescription">{{ __('Warehouses for rent in Budapest! Rent from traditional and serviced warehouses on favourable lease terms, with a review of the entire warehouse market database. Offers sent the same day.') }}</x-slot>

<div class="overflow-hidden max-w-[2200px] mx-auto bg-white">
    <div>

        <div class="relative">

            <div class="kategoria-valaszto grid grid-cols-1 md:grid-cols-3 font-bold">
                <a wire:navigate.hover href="{{ localized_route('budapest.category', ['category' => 'pesti-oldal']) }}"
                    class="hero-image relative group">
                    <img src="{{ Vite::asset('resources/images/pesti_oldal.webp') }}" alt="{{ __('Pest side and agglomeration') }}"
                        class="w-full h-auto object-cover aspect-3/2" loading="lazy" />
                    <div
                        class="flex items-center justify-center text-xl text-center text-white bg-primary backdrop-blur-xl h-18 group-hover:bg-accent duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                            {{ __('Pest side and agglomeration') }}</h2>
                    </div>
                </a>
                <a wire:navigate.hover href="{{ localized_route('budapest.category', ['category' => 'budai-oldal']) }}"
                    class="hero-image relative group">
                    <img src="{{ Vite::asset('resources/images/budai_oldal.webp') }}" alt="{{ __('Buda side') }}"
                        class="w-full h-auto object-cover aspect-3/2" loading="lazy" />
                    <div
                        class="flex items-center justify-center text-xl text-center text-white bg-logogray backdrop-blur-xl h-18 group-hover:bg-accent duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                            {{ __('Buda side') }}</h2>
                    </div>
                </a>
                <a wire:navigate.hover href="{{ localized_route('budapest.category', ['category' => 'videki-raktarak']) }}"
                    class="hero-image relative group">
                    <img src="{{ Vite::asset('resources/images/videki_raktarak.webp') }}" alt="{{ __('Warehouses in the countryside') }}"
                        class="w-full h-auto object-cover aspect-3/2" loading="lazy" />
                    <div
                        class="flex items-center justify-center text-xl text-center text-white bg-primary backdrop-blur-xl h-18 group-hover:bg-accent duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                        <h2 class="group-hover:scale-110 duration-1000 transition-all ease-[cubic-bezier(0.19,1,0.22,1)]">
                            {{ __('Warehouses in the countryside') }}</h2>
                    </div>
                </a>
            </div>

        </div>
        <x-office-search-form />

        <x-home.featured-offers :properties="$this->featuredProperties" />

        <x-home.references :references="$this->references" />

        @if (app()->getLocale() === 'hu')
            <x-home.blog :posts="$this->latestPosts" />

            <x-home.news :news="$this->latestNews" />
        @endif

    </div>
</div>
