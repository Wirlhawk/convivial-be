<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static ?string $breadcrumb = "List Akun";
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Akun Baru')
                ->icon('heroicon-o-plus')
                ->url(static::getResource()::getUrl('create'))
                ->color('primary'),
        ];
    }

}
