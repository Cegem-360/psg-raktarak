<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPageResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ContactPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditContactPage extends EditRecord
{
    protected static string $resource = ContactPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
