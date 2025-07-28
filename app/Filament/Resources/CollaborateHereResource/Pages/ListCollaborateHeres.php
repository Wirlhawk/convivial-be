<?php

namespace App\Filament\Resources\CollaborateHereResource\Pages;

use App\Filament\Resources\CollaborateHereResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListCollaborateHeres extends ListRecords
{
    protected static string $resource = CollaborateHereResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->modal()
                ->modalHeading('Tambah Kolaborasi')
                ->modalSubmitActionLabel('Simpan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kolaborasi berhasil ditambahkan!')
                        ->body('Data kolaborasi baru telah berhasil disimpan.')
                ),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\EditAction::make()
                    ->modal()
                    ->modalHeading('Edit Kolaborasi')
                    ->modalSubmitActionLabel('Update')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Kolaborasi berhasil diupdate!')
                            ->body('Data kolaborasi telah berhasil diubah.')
                    ),
                \Filament\Actions\DeleteAction::make(),
            ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            // Contoh filter: filter by title
            \Filament\Tables\Filters\SelectFilter::make('title')
                ->options(fn () => \App\Models\CollaborateHere::query()->pluck('title', 'title')->toArray()),
        ];
    }
}
