<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getHeading(): string
    {
        return 'Ubah Akun 
        ' . $this->getRecord()->name;
    }

    protected function getRedirectUrl(): string
    {
        return
            $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification {
        $akun = $this->getRecord();
        return Notification::make()
        ->success()
        ->title('Data Akun berhasil diubah')
        ->body("Akun yang bernama{$akun->name} berhasil diubah.");
    }

    public function mutateFormDataBeforeSave(array $data): array {
        if(array_key_exists('new_password', $data) || filled($data['new_password'])) {
            $this->record->password = Hash::make($data['new_password']);
        }
        return $data;
    }
}
