<?php

namespace App\Filament\Resources\JenisCucianResource\Pages;

use App\Filament\Resources\JenisCucianResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListJenisCucians extends ListRecords

{
    protected static ?string $breadcrumb = "List Jenis Cucian";
    protected static string $resource = JenisCucianResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Jenis Cucian Baru')
                ->icon('heroicon-o-plus')
                ->url(static::getResource()::getUrl('create'))
                ->color('primary'),
        ];
    }
}
