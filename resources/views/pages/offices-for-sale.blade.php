<x-slot name="title">{{ __('Warehouses for sale in Budapest | PSG Raktárak') }}</x-slot>
<x-slot name="metaDescription">{{ __('Warehouses and logistics properties for sale in Budapest: current listings with area, price and location. Investment warehouse properties from PSG Raktárak.') }}</x-slot>

<div class="overflow-hidden max-w-[2200px] mx-auto bg-white">
    <div>

        <livewire:list-sale-offices />

        <x-office-search-form />

    </div>
</div>
