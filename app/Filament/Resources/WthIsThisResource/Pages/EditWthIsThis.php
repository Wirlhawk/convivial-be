<?php

namespace App\Filament\Resources\WthIsThisResource\Pages;

use App\Filament\Resources\WthIsThisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWthIsThis extends EditRecord
{
    protected static string $resource = WthIsThisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
