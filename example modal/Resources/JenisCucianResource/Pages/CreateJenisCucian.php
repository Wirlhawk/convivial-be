<?php

namespace App\Filament\Resources\JenisCucianResource\Pages;

use App\Filament\Resources\JenisCucianResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisCucian extends CreateRecord
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
            ->label(__('Simpan dan Buat Jenis Cucian lagi'))
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

    protected static ?string $title = 'Buat Jenis Cucian Baru';
    protected static ?string $breadcrumb = "Buat Jenis Cucian";

    protected function getCreatedNotification(): ?Notification
    {
        $jeniscucian = $this->record->nama_jenis_cucian;
        $recipient = auth()->user();

        return Notification::make()
            ->title(__('Jenis Cucian berhasil dibuat!'))
            ->body(__('Jenis Cucian dengan nama : ' . $jeniscucian . ' telah berhasil disimpan.'))
            ->success()
            ->sendToDatabase($recipient);
    }
    protected static string $resource = JenisCucianResource::class;
}
