<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\FavoriteProperties;
use App\Services\FavoritesExcelExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Downloads the visitor's favorite properties as an Excel sheet.
 */
final readonly class ExportFavorites
{
    public function __construct(
        private FavoriteProperties $favorites,
        private FavoritesExcelExportService $export,
    ) {}

    public function __invoke(): BinaryFileResponse
    {
        return response()
            ->download($this->export->generate($this->favorites->properties()), 'P.S.G. RAKTÁR ajánlatok.xlsx')
            ->deleteFileAfterSend();
    }
}
