<?php

namespace App\Filament\Resources\JenisTimbanganResource\Pages;

use App\Filament\Resources\JenisTimbanganResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisTimbangan extends CreateRecord
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
            ->label(__('Simpan dan Buat Jenis Timbangan lagi'))
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

    protected static ?string $title = 'Buat Jenis Timbangan Baru';
    protected static ?string $breadcrumb = "Buat Jenis Timbangan";

    protected function getCreatedNotification(): ?Notification
    {
        $jenistimbangan = $this->record->nama_jenis_timbangan;
        $recipient = auth()->user();

        return Notification::make()
            ->title(__('Jenis Timbangan berhasil dibuat!'))
            ->body(__('Jenis Timbangan dengan nama : ' . $jenistimbangan . ' telah berhasil disimpan.'))
            ->success()
            ->sendToDatabase($recipient);
    }
    protected static string $resource = JenisTimbanganResource::class;
}
