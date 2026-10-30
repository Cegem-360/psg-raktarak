<x-slot name="title">{{ __('News') }} | PSG-RAKTÁRAK</x-slot>
<x-slot name="meta">
    <meta name="description"
        content="{{ __('PSG-RAKTÁRAK news - The latest news, developments and professional insights from the warehouse market.') }}">
    <meta name="keywords" content="{{ __('news, warehouse market, real estate news, warehouses, warehouse rental, industry news') }}">
</x-slot>

<div>
    <x-page-section>
        <x-slot:heading>{{ __('News') }}</x-slot:heading>

        <livewire:news-list />
    </x-page-section>
</div>
