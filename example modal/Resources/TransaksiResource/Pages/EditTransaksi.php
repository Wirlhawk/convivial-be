<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTransaksi extends EditRecord
{

    public function getHeading(): string
    {
        return 'Ubah Transaksi 
        ' . $this->getRecord()->id_transaksi;
    }
    protected static string $resource = TransaksiResource::class;

    protected function getRedirectUrl(): string
    {
        return
            $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification {
        $transaksi = $this->getRecord();
        return Notification::make()
        ->success()
        ->title('Transaksi berhasil diubah')
        ->body("ID Transaksi {$transaksi->id_transaksi} berhasil diubah.");
    }
}
