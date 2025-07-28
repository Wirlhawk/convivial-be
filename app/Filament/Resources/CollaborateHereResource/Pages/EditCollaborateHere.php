<?php

namespace App\Filament\Resources\CollaborateHereResource\Pages;

use App\Filament\Resources\CollaborateHereResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollaborateHere extends EditRecord
{
    protected static string $resource = CollaborateHereResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
