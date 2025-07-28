<?php

namespace App\Filament\Resources\WhatUpDetailResource\Pages;

use App\Filament\Resources\WhatUpDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatUpDetail extends EditRecord
{
    protected static string $resource = WhatUpDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
