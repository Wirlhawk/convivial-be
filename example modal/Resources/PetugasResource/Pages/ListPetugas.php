<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPetugas extends ListRecords
{
    protected static ?string $breadcrumb = "List Petugas";
    protected static string $resource = PetugasResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Petugas Baru')
                ->icon('heroicon-o-plus')
                ->url(static::getResource()::getUrl('create'))
                ->color('primary'),
        ];
    }
}
