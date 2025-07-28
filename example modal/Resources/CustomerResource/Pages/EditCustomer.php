<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    public function getHeading(): string
    {
        return 'Ubah Customer 
        ' . $this->getRecord()->nama_customer;
    }

    protected function getRedirectUrl(): string
    {
        return
            $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification {
        $customer = $this->getRecord();
        return Notification::make()
        ->success()
        ->title('Customer berhasil diubah')
        ->body("Customer dengan nama {$customer->nama_customer} berhasil diubah.");
    }
}
