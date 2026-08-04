<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Property;
use App\Services\FavoritesExcelExportService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FavoritesList extends Component
{
    public $favoriteProperties = [];

    public $favoriteCount = 0;

    protected $listeners = [
        'favorites-updated' => 'refreshFavorites',
        'refresh-favorites' => 'refreshFavorites',
        'openSendFavoritesModal' => 'openSendFavoritesModal',
    ];

    public function mount(): void
    {
        $this->loadFavorites();
    }

    public function refreshFavorites(): void
    {
        $this->loadFavorites();
    }

    public function openSendFavoritesModal(): void
    {
        $this->dispatch('openSendFavoritesModal')->to('favorites-send-modal');
    }

    public function downloadExcel(): StreamedResponse
    {
        $this->loadFavorites();

        if ($this->favoriteCount === 0) {
            $this->dispatch('notify', message: 'Nincsenek kedvenc ingatlanok.');

            return response()->streamDownload(function (): void {}, 'empty.xlsx');
        }

        $service = resolve(FavoritesExcelExportService::class);
        $tempFile = $service->generate(collect($this->favoriteProperties));

        return response()->streamDownload(function () use ($tempFile): void {
            echo file_get_contents($tempFile);
            @unlink($tempFile);
        }, 'P.S.G. RAKTÁR ajánlatok.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render(): Factory|View
    {
        return view('livewire.favorites-list', [
            'favoriteProperties' => $this->favoriteProperties,
            'favoriteCount' => $this->favoriteCount,
        ]);
    }

    private function loadFavorites(): void
    {
        $favorites = $this->getFavorites();
        $this->favoriteCount = count($favorites);

        if ($favorites !== []) {
            $this->favoriteProperties = Property::whereIn('id', $favorites)
                ->active()
                ->get();
        } else {
            $this->favoriteProperties = collect();
        }
    }

    private function getFavorites(): array
    {
        try {
            $favorites = Cookie::get('property_favorites', '[]');
            $decoded = json_decode($favorites, true);

            return is_array($decoded) ? $decoded : [];
        } catch (Exception $exception) {
            return [];
        }
    }
}
