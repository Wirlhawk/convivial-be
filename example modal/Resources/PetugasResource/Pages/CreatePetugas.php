<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePetugas extends CreateRecord
{

    protected static bool $canCreateAnother = true;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('Simpan'))
            ->submit('create')
            ->color('primary')
            ->keyBindings(['mod+s']);
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('Simpan dan Buat Petugas lagi'))
            ->submit('createAnother')
            ->color('secondary')
            ->keyBindings(['mod+shift+s']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('Batal'))
            ->url($this->getResource()::getUrl('index'))
            ->color('gray')
            ->keyBindings(['mod+b']);
    }

    protected static ?string $title = 'Buat Petugas Baru';
    protected static ?string $breadcrumb = "Buat Petugas";

    protected function getCreatedNotification(): ?Notification
    {
        $petugas = $this->record->nama_petugas;
        $recipient = auth()->user();

        return Notification::make()
            ->title(__('Petugas berhasil dibuat!'))
            ->body(__('Petugas dengan nama : ' . $petugas . ' telah berhasil disimpan.'))
            ->success()
            ->sendToDatabase($recipient);
    }
    protected static string $resource = PetugasResource::class;
}
