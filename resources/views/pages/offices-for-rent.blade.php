<x-slot name="title">{{ __('Warehouses for rent in Budapest | PSG Raktárak') }}</x-slot>
<x-slot name="metaDescription">{{ __('Warehouses for rent in Budapest in one place: current available spaces, rental and operating fees of warehouses on a map. Request an offer today!') }}</x-slot>

<div class="overflow-hidden max-w-[2200px] mx-auto bg-white">
    <div>

        <livewire:list-rent-offices />

        <x-office-search-form />

    </div>
</div>
