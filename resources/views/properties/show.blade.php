@use('App\Models\Translate')
<x-layouts.app>
    <x-slot name="title">
        @if (app()->getLocale() === 'en' && $property->meta_title_en)
            {{ $property->meta_title_en }}
        @elseif ($property->meta_title)
            {{ $property->meta_title }}
        @endif
    </x-slot>

    <x-slot name="metaDescription">
        @if (app()->getLocale() === 'en' && $property->meta_description_en)
            {{ $property->meta_description_en }}
        @elseif ($property->meta_description)
            {{ $property->meta_description }}
        @endif
    </x-slot>

    <x-slot name="metaKeywords">
        @if (app()->getLocale() === 'en' && $property->meta_keywords_en)
            {{ $property->meta_keywords_en }}
        @elseif ($property->meta_keywords)
            {{ $property->meta_keywords }}
        @endif
    </x-slot>
    @if ($property->status !== 'active')
        <x-slot name="metaRobots">noindex, nofollow</x-slot>
        <x-slot name="metaGooglebot">noindex, nofollow</x-slot>
    @endif
    <div class="relative bg-fixed bg-center bg-no-repeat bg-cover"
        style="background-image: url({{ Vite::asset('resources/images/engineer-plan-green-railway-project-with-infrastru-2025-01-10-03-41-57-utc.webp') }});">
        <div class="absolute inset-0 z-1 bg-gradient-to-b from-white/90 to-white/70"></div>
        <div class="container relative z-10 pt-24 pb-20 mx-auto space-y-8">
            <h2 class="mt-4 mb-16 text-5xl text-center font-font-bold drop-shadow text-logogray/80">
                @if (app()->getLocale() === 'en')
                    {{ Translate::whereName($property->title)->first()?->translated ?: $property->title }}
                @else
                    {{ $property->title }}
                @endif
            </h2>
            <div
                class="grid max-w-screen-xl grid-cols-1 gap-8 p-8 mx-auto border shadow-xl md:grid-cols-2 backdrop-blur-3xl rounded-xl border-white/15">
                <div>
                    <x-cards.ingatlan-gallery-carousel :images="collect($property->property_photos)" :title="$property->title" />
                </div>
                <div class="p-4">

                    <table class="w-full mt-4 table-auto">
                        <tbody>
                            @if ($property->elado_v_kiado === 'elado-raktar')
                                @if ($property->cim_irsz || $property->cim_varos)
                                    <tr>
                                        <td class="font-bold">{{ __('Address') }}:</td>
                                        <td>{{ $property->cim_irsz }} {{ $property->cim_varos }},</td>
                                    </tr>
                                @endif
                                @if ($property->total_area)
                                    <tr>
                                        <td class="font-bold">{{ __('Total Area') }}:</td>
                                        <td>{{ $property->total_area }}
                                            {{ __($property->osszterulet_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->min_berleti_dij)
                                    <tr>
                                        <td class="font-bold">{{ __('Price') }}:</td>
                                        <td>{{ $property->min_berleti_dij }}
                                            {{ __($property->min_berleti_dij_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->parkolas)
                                    <tr>
                                        <td class="font-bold">{{ __('Parking') }}:</td>
                                        <td>{{ __($property->parkolas) }}</td>
                                    </tr>
                                @endif
                                @if ($property->kodszam)
                                    <tr>
                                        <td class="font-bold">{{ __('Code') }}:</td>
                                        <td>{{ $property->kodszam }}</td>
                                    </tr>
                                @endif
                            @else
                                {{-- Default fields for rental offices --}}
                                <tr>
                                    <td class="font-bold">{{ __('Address') }}:</td>
                                    <td>{{ $property->cim_irsz }} {{ $property->cim_varos }},
                                        {{ $property->cim_utca }}
                                        {{ __($property->cim_utca_addons ?? '') }} {{ $property->cim_hazszam }}
                                    </td>
                                </tr>
                                @if ($property->construction_year)
                                    <tr>
                                        <td class="font-bold">{{ __('Construction Year') }}:</td>
                                        <td>{{ $property->construction_year }}</td>
                                    </tr>
                                @endif
                                @if ($property->total_area)
                                    <tr>
                                        <td class="font-bold">{{ __('Total Area') }}:</td>
                                        <td>{{ $property->total_area }}
                                            {{ __($property->osszterulet_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->jelenleg_kiado)
                                    <tr>
                                        <td class="font-bold">{{ __('Currently Available') }}:</td>
                                        <td>{{ $property->jelenleg_kiado }} m²</td>
                                    </tr>
                                @endif
                                @if ($property->min_kiado)
                                    <tr>
                                        <td class="font-bold">{{ __('Min. Available') }}:</td>
                                        <td>{{ $property->min_kiado }}
                                            {{ __($property->min_kiado_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->min_berleti_dij)
                                    <tr>
                                        <td class="font-bold">{{ __('Rent') }}:</td>
                                        <td>{{ $property->min_berleti_dij }}{{ $property->max_berleti_dij && $property->max_berleti_dij !== $property->min_berleti_dij ? ' - ' . $property->max_berleti_dij : '' }}
                                            {{ __($property->min_berleti_dij_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->uzemeletetesi_dij)
                                    <tr>
                                        <td class="font-bold">{{ __('Operating Fee') }}:</td>
                                        <td>{{ $property->uzemeletetesi_dij }}
                                            {{ __($property->uzemeletetesi_dij_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->raktar_terulet)
                                    <tr>
                                        <td class="font-bold">{{ __('Office Area') }}:</td>
                                        <td>{{ number_format((int) $property->raktar_terulet) }}
                                            {{ __($property->raktar_terulet_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->raktar_berleti_dij)
                                    <tr>
                                        <td class="font-bold">{{ __('Office Rent') }}:</td>
                                        <td>{{ $property->raktar_berleti_dij }}
                                            {{ __($property->raktar_berleti_dij_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->parkolas)
                                    <tr>
                                        <td class="font-bold">{{ __('Parking') }}:</td>
                                        <td>{{ $property->parkolas }}</td>
                                    </tr>
                                @endif
                                @if ($property->min_parkolas_dija)
                                    <tr>
                                        <td class="font-bold">{{ __('Parking Fee') }}:</td>
                                        <td>{{ $property->min_parkolas_dija }}{{ $property->max_parkolas_dija ? ' - ' . $property->max_parkolas_dija : '' }}
                                            {{ __($property->min_parkolas_dija_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->kozos_teruleti_arany)
                                    <tr>
                                        <td class="font-bold">{{ __('Common Area Ratio') }}:</td>
                                        <td>{{ $property->kozos_teruleti_arany }}
                                            {{ __($property->kozos_teruleti_arany_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->min_berleti_idoszak)
                                    <tr>
                                        <td class="font-bold">{{ __('Min. Rental Period') }}:</td>
                                        <td>
                                            {{ $property->min_berleti_idoszak }}
                                            {{ __($property->min_berleti_idoszak_addons ?? '') }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($property->kodszam)
                                    <tr>
                                        <td class="font-bold">{{ __('Code') }}:</td>
                                        <td>{{ $property->kodszam }}</td>
                                    </tr>
                                @endif
                                @if (!$property->jelenleg_kiado)
                                    <tr>
                                        <td class="py-8 text-xl italic text-center text-red-500 font-font-bold"
                                            colspan="2">
                                            {{ __('The office building is currently 100% rented out!') }}
                                        </td>
                                    </tr>
                                @endif

                                @if ($property->vat)
                                    <tr>
                                        <td style="padding-top: 20px" class="font-bold" colspan="2">
                                            @if ($property->vat)
                                                {{ __('The above fees are subject to an additional 27% VAT!') }}
                                            @else
                                                {{ __('The above fees are NOT subject to an additional 27% VAT!') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if (auth()->check() || request()->hasValidSignature())
                <div class="max-w-screen-xl p-8 mx-auto border shadow-xl backdrop-blur-3xl rounded-xl border-white/15">
                    <div class="text-center">
                        <h3 class="mb-4 text-2xl font-bold">{{ __('Property Details') }}</h3>
                        <p class="mb-6 text-gray-600">{{ __('Download detailed information about this property') }}</p>

                        <a href="{{ URL::signedRoute('property.pdf', ['property' => $property->id]) }}"
                            class="inline-flex items-center px-6 py-3 font-medium text-white transition-colors rounded-lg bg-accent hover:bg-accent/90">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            {{ __('Printable Version') }}
                        </a>

                    </div>

                </div>
            @endif
            <div
                class="grid max-w-screen-xl grid-cols-1 gap-8 p-8 mx-auto border shadow-xl md:grid-cols-2 backdrop-blur-3xl rounded-xl border-white/15">
                <div>
                    @if (!$property->isSale())
                        @if ($property->maps_lat && $property->maps_lng)
                            {{-- Ingyenes: Koordináta alapú térkép pin-nel --}}
                            <iframe
                                src="https://maps.google.com/maps?q={{ $property->maps_lat }},{{ $property->maps_lng }}&hl={{ app()->getLocale() }}&z=16&output=embed"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @else
                            {{-- Ingyenes: Cím alapú keresés pin-nel --}}
                            <iframe
                                src="https://maps.google.com/maps?q={{ urlencode($property->cim_irsz . ' ' . $property->cim_varos . ', ' . $property->cim_utca . ' ' . $property->cim_hazszam . ($property->cim_utca_addons ? ', ' . $property->cim_utca_addons : '')) }}&hl={{ app()->getLocale() }}&z=16&output=embed"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @endif
                    @else
                        <x-forms.contact :selected_property_id="$property->id" :selected_property_type_is_rent="$property->isRent()" />
                    @endif

                </div>
                <div class="p-4">
                    <h2 class="text-3xl">
                        {{ __(':title Presentation', ['title' => app()->getLocale() === 'en' ? (Translate::whereName($property->title)->first()?->translated ?: $property->title) : $property->title]) }}
                    </h2>
                    <div class="mt-4 space-y-4">
                        <div class="leading-relaxed text-justify">
                            @if (app()->getLocale() === 'en' && $property->en_content)
                                {!! $property->en_content !!}
                            @elseif ($property->content)
                                {!! $property->content !!}
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @if (!$property->isSale())
                <div
                    class="grid max-w-screen-xl grid-cols-1 gap-8 p-8 mx-auto border shadow-xl md:grid-cols-2 backdrop-blur-3xl rounded-xl border-white/15">
                    <div class="order-2 md:order-none">
                        <section class="bg-white rounded-xl">
                            <div class="max-w-screen-md px-4 py-8 mx-auto lg:py-16">
                                <h2 class="mb-4 text-4xl tracking-tight text-center font-extrafont-bold text-accent">
                                    {{ __('Contact Us!') }}</h2>
                                <p class="mb-8 font-light text-center text-gray-500 lg:mb-16 sm:text-xl">
                                    {{ __('Request a personalized offer online!') }}</p>
                                <x-forms.contact :selected_property_id="$property->id" :selected_property_type_is_rent="$property->isRent()" />
                            </div>
                        </section>

                    </div>
                    <div class="order-1 p-4 space-y-4 md:order-none">
                        <div class="space-y-4">
                            <h2 class="text-3xl">{{ __('Features') }}</h2>
                            <ul class="text-lg list-disc sm:columns-2 gap-x-8 gap-y-3">

                                @if ($property->services || $property->tags)
                                    @php
                                        $allItems = collect($property->services)
                                            ->merge($property->tags ?? [])
                                            ->sortBy(function ($item) {
                                                // Ékezetek eltávolítása és kisbetűsítés a rendezéshez
                                                $normalized = strtolower($item->name);
                                                $normalized = str_replace(
                                                    ['á', 'é', 'í', 'ó', 'ő', 'ú', 'ű', 'ü', 'ö'],
                                                    ['a', 'e', 'i', 'o', 'o', 'u', 'u', 'u', 'o'],
                                                    $normalized,
                                                );
                                                return $normalized;
                                            });
                                    @endphp
                                    @foreach ($allItems as $item)
                                        <li class="pb-1 jellemzok">
                                            @if (app()->getLocale() === 'en')
                                                {{ Translate::whereName($item->name)->first()?->translated ?? $item->name }}
                                            @else
                                                {{ $item->name }}
                                            @endif
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="relative bg-center bg-no-repeat bg-cover"
        style="background-image: url({{ Vite::asset('resources/images/cargo-loading-dock-doors-of-big-warehouse-building-2024-10-31-00-59-14-utc.webp') }});">
        <div class="absolute inset-0 z-1 bg-gradient-to-b from-white to-white/30"></div>
        <div class="container relative z-10 pt-12 pb-20 mx-auto kiemelt-ajanlatok">
            <h2 class="mt-4 mb-16 text-5xl text-center font-font-bold drop-shadow text-logogray/80">
                {{ __('Similar Offices') }}</h2>
            <div class="grid max-w-screen-xl grid-cols-1 gap-4 mx-auto md:grid-cols-2 lg:grid-cols-3">
                @foreach ($similarProperties ?? [] as $similarProperty)
                    @if ($similarProperty->isRent())
                        <x-cards.ingatlan-card
                            image="{{ $similarProperty->getFirstImageUrl() ?: Vite::asset('resources/images/default-office.jpg') }}"
                            title="{{ $similarProperty->title }}" :description="$similarProperty->getAddressFormated()"
                            link="{{ route('properties.show', $similarProperty->slug) }}" />
                    @else
                        <x-cards.ingatlan-card
                            image="{{ $similarProperty->getFirstImageUrl() ?: Vite::asset('resources/images/default-office.jpg') }}"
                            title="{{ $similarProperty->title }}" :description="$similarProperty->getAddressFormatedForSale()"
                            link="{{ route('properties.show-for-sale', $similarProperty->slug) }}" />
                    @endif
                @endforeach

            </div>
        </div>
    </div>

</x-layouts.app>
