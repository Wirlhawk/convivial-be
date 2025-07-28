<?php

namespace App\Filament\Resources\WthIsThisResource\Pages;

use App\Filament\Resources\WthIsThisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListWthIsThis extends ListRecords
{
    protected static string $resource = WthIsThisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->modal()
                ->modalHeading('Tambah WthIsThis')
                ->modalSubmitActionLabel('Simpan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('WthIsThis berhasil ditambahkan!')
                        ->body('Data baru telah berhasil disimpan.')
                ),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\EditAction::make()
                    ->modal()
                    ->modalHeading('Edit WthIsThis')
                    ->modalSubmitActionLabel('Update')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('WthIsThis berhasil diupdate!')
                            ->body('Data telah berhasil diubah.')
                    ),
                \Filament\Actions\DeleteAction::make(),
            ]),
        ];
    }
}
