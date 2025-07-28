<?php

namespace App\Filament\Resources\JenisCucianResource\Pages;

use App\Filament\Resources\JenisCucianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisCucian extends EditRecord
{
    protected static string $resource = JenisCucianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
