<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static ?string $breadcrumb = "List Customer";
    protected static string $resource = CustomerResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Customer Baru')
                ->icon('heroicon-o-plus')
                ->url(static::getResource()::getUrl('create'))
                ->color('primary'),
        ];
    }
}
