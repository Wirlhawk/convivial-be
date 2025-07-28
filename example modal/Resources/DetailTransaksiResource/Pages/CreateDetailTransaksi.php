<?php

namespace App\Filament\Resources\DetailTransaksiResource\Pages;

use App\Filament\Resources\DetailTransaksiResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailTransaksi extends CreateRecord
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
            ->label(__('Simpan dan Buat Detail Transaksi lagi'))
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

    protected static ?string $title = 'Buat Detail Transaksi Baru';
    protected static ?string $breadcrumb = "Buat Detail Transaksi";

    protected function getCreatedNotification(): ?Notification
    {
        $detailTransaksi = $this->record->id_detail_transaksi;

        return Notification::make()
            ->title(__('Detail Transaksi berhasil dibuat!'))
            ->body(__('Detail Transaksi dengan ID : ' . $detailTransaksi . ' telah berhasil disimpan.'))
            ->success()
            ->send();
    }
    protected static string $resource = DetailTransaksiResource::class;
}
