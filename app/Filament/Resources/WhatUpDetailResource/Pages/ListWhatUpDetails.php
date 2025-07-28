<?php

namespace App\Filament\Resources\WhatUpDetailResource\Pages;

use App\Filament\Resources\WhatUpDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatUpDetails extends ListRecords
{
    protected static string $resource = WhatUpDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modal(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\EditAction::make()->modal(),
                Actions\DeleteAction::make(),
            ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            // Contoh filter: filter by title
            \Filament\Tables\Filters\SelectFilter::make('title')
                ->options(fn () => \App\Models\WhatUpDetail::query()->pluck('title', 'title')->toArray()),
        ];
    }
}
