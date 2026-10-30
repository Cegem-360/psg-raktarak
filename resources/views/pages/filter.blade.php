<x-slot name="title">{{ $category->seo_title ?: __(':category in Budapest | PSG Raktárak', ['category' => $category->name]) }}</x-slot>
<x-slot name="metaDescription">{{ $category->meta_description ?: __(':category in Budapest: current available spaces, rental and operating fees of warehouses on a map. Request a personalised offer from PSG Raktárak!', ['category' => $category->name]) }}</x-slot>

<div>
    <livewire:list-rent-offices :query-params="$queryParams" />
</div>
