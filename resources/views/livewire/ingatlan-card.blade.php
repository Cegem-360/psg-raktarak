<div
    class="{{ $swiper ? 'swiper-slide' : '' }} group relative bg-white/10 rounded-xl overflow-hidden shadow-xl backdrop-blur-3xl hover:brightness-95 transition-all duration-300 ease-in-out border border-white/15">
    <div class="relative">

        <!-- Swiper slides for images -->
        @if ($minicarousel && count($images) > 0)
            <div class="swiper minicarousel-swiper !grid">
                <div class="swiper-wrapper">
                    @foreach ($images as $carouselimage)
                        <div class="swiper-slide">
                            <img src="{{ Storage::url($carouselimage) }}" alt="{{ $title }}"
                                class="w-full h-auto object-cover aspect-[3/2]" loading="lazy" />
                        </div>
                    @endforeach
                </div>
                <div
                    class="swiper-button-prev minicarousel-button-prev !text-accent shadow bg-white/40 hover:bg-white/60 hover:shadow rounded after:!text-xl after:!font-bold after:drop-shadow">
                </div>
                <div
                    class="swiper-button-next minicarousel-button-next !text-accent shadow bg-white/40 hover:bg-white/60 hover:shadow rounded after:!text-xl after:!font-bold after:drop-shadow">
                </div>
            </div>
        @endif
        <!-- Fallback image if no swiper -->
        @if (!$minicarousel && $image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-auto object-cover aspect-[3/2]"
                loading="lazy" />
        @endif

        <!-- Favorite button in top-right corner -->
        @auth
            @if ($property)
                <button wire:click="toggleFavorite"
                    class="absolute top-2 right-2 p-2 rounded-full shadow-lg transition-all duration-200 z-10
                   {{ $favoritestatus ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-white/80 hover:bg-white text-gray-700 hover:text-red-500' }}">
                    <svg class="w-5 h-5" fill="{{ $favoritestatus ? 'currentColor' : 'none' }}" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            @endif
        @endauth

        <!-- Icon boxes positioned at bottom-right of image -->
        <div class="absolute bottom-2 right-5 flex gap-1 z-10">
            <!-- Map icon box -->
            <button wire:click="showMapModal"
                class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded shadow-lg transition-colors duration-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Phone icon box -->
            <button wire:click="showPhoneModal"
                class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded shadow-lg transition-colors duration-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
            </button>
        </div>
    </div>

    <div class="{{ $small ? 'p-3 pl-4' : 'p-6 pl-8' }}">
        <h3 class="{{ $small ? 'text-lg' : 'text-xl' }} font-bold mb-2">{{ $title }}</h3>
        <p class="{{ $small ? 'text-gray-700 text-base min-h-16' : 'text-gray-700 min-h-24' }}">
            {!! $description ?? 'Nincs' !!}
        </p>
        <a href="{{ $link ?? '#' }}"
            class="inline-block {{ $small ? 'mb-2 px-3 py-1 text-sm' : 'mb-4 px-6 py-2' }} bg-primary/70 text-white rounded group-hover:bg-primary/90 transition-colors duration-300 ease-in-out">
            {{ __('More details') }}
        </a>
    </div>
</div>
