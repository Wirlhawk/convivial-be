<?php

namespace App\Filament\Resources\JenisTimbanganResource\Pages;

use App\Filament\Resources\JenisTimbanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisTimbangans extends ListRecords
{
    protected static string $resource = JenisTimbanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
