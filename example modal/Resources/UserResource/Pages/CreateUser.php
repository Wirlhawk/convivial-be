<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
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
            ->label(__('Simpan dan Buat Akun Petugas lagi'))
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

    protected static ?string $title = 'Buat Akun Petugas Baru';
    protected static ?string $breadcrumb = "Buat Akun";

    protected function getCreatedNotification(): ?Notification
    {
        $akunPid = $this->record->name;
        $recipient = auth()->user();
        return Notification::make()
        
            ->title(__('Buat Akun berhasil dibuat!'))
            ->body(__('Buat Akun dengan nama : ' . $akunPid . ' telah berhasil disimpan.'))
            ->success()
            ->sendToDatabase($recipient);
    }
    protected static string $resource = UserResource::class;
}
