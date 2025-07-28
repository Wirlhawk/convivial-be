<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\HasWizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksi extends CreateRecord
{
    use HasWizard;
    protected static bool $canCreateAnother = true;

    public function getStep(): array
    {
        return [
            Step::make('Informasi Transaksi')
                ->schema([
                    Section::make('Informasi Transaksi')
                        ->description('Pilih nama customer yang akan mencuci')
                        ->icon('heroicon-m-user-group')
                        ->iconColor('primary')
                        ->schema(TransaksiResource::getInformasiTransaksi())
                ]),

            Step::make('Detail Transaksi')
                ->schema([
                    Section::make('Detail Transaksi')
                        ->description('Masukkan detail transaksi berdasarkan jenis cucian yang dipilih')
                        ->icon('heroicon-m-shopping-bag')
                        ->iconColor('warning')
                        ->schema(TransaksiResource::getDetailTransaksi())
                ]),

            Step::make('Total & Estimasi')
                ->schema([
                    Section::make('Total & Estimasi')
                        ->description('Lihat total harga dan estimasi selesai transaksi')
                        ->icon('heroicon-m-currency-dollar')
                        ->iconColor('success')
                        ->schema(TransaksiResource::getTotalDanEstimasi())
                ]),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('Simpan'))
            ->submit('create')
            ->color('secondary')
            ->keyBindings(['mod+s'])
            ->hidden();
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('Simpan dan Buat Transaksi lagi'))
            ->submit('createAnother')
            ->color('primary')
            ->keyBindings(['mod+shift+s'])
            ->hidden();
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
            ->keyBindings(['mod+b'])
            ->hidden();
    }

    protected static string $resource = TransaksiResource::class;

    protected static ?string $title = 'Buat Transaksi Baru';
    protected static ?string $breadcrumb = "Buat Transaksi";

    protected function getCreatedNotification(): ?Notification
    {
        $transaksiId = $this->record->id_transaksi;
        $recipient = auth()->user();

        return Notification::make()
            ->title(__('Transaksi berhasil dibuat!'))
            ->body(__('Transaksi dengan ID : ' . $transaksiId . ' telah berhasil disimpan.'))
            ->success()
            ->sendToDatabase($recipient);
    }
}
