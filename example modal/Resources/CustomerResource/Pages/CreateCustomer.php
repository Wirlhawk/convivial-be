<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
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
            ->label(__('Simpan dan Buat Customer lagi'))
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

    protected static ?string $title = 'Buat Customer Baru';
    protected static ?string $breadcrumb = "Buat Customer";

    protected function getCreatedNotification(): ?Notification
    {
        $customer = $this->record->nama_customer;

        return Notification::make()
            ->title(__('Customer berhasil dibuat!'))
            ->body(__('Customer dengan nama : ' . $customer . ' telah berhasil disimpan.'))
            ->success()
            ->send();
    }
    protected static string $resource = CustomerResource::class;
}
