<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->modal()
                ->modalHeading('Tambah Tim')
                ->modalSubmitActionLabel('Simpan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Tim berhasil ditambahkan!')
                        ->body('Data tim baru telah berhasil disimpan.')
                ),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\EditAction::make()
                    ->modal()
                    ->modalHeading('Edit Tim')
                    ->modalSubmitActionLabel('Update')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Tim berhasil diupdate!')
                            ->body('Data tim telah berhasil diubah.')
                    ),
                \Filament\Actions\DeleteAction::make(),
            ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            // Contoh filter: filter by role_name
            \Filament\Tables\Filters\SelectFilter::make('role_name')
                ->options(fn () => \App\Models\Team::query()->pluck('role_name', 'role_name')->toArray()),
        ];
    }
}
