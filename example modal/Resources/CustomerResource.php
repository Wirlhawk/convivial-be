<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?string $pluralLabel = 'Customer'; // Override nama plural
    protected static ?string $label = 'Customer'; // Override nama singular
    protected static ?string $navigationLabel = 'Customer';
    protected static ?string $slug = 'member/customer';
    protected static ?string $navigationGroup = 'Manajemen Anggota';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_customer')
                    ->label('Nama Customer')
                    ->placeholder('Masukkan Nama Customer')
                    ->rules(['required'])
                    ->unique(ignoreRecord: true),
                PhoneInput::make('no_telp')
                    ->label('Nomor Telepon')
                    ->required()
                    ->onlyCountries(['ID', 'SG', 'MY', 'TH'])
                    ->defaultCountry('ID')
                    ->countrySearch(true)
                    ->showFlags(true)
                    ->formatAsYouType(true)
                    ->rules(['regex:/^[0-9+]*$/']),
                Textarea::make('alamat')
                    ->label('Masukkan Alamat Customer')
                    ->placeholder('Masukkan Alamat Customer')
                    ->required(),
                Select::make('tipe_customer')
                    ->placeholder('Pilih tipe customer')
                    ->label('Tipe Customer')
                    ->options([
                        'guest' => 'Guest',
                        'membership' => 'Membership',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_customer')
                    ->label('Nama Customer')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary'),
                TextColumn::make('no_telp')
                    ->label('Nomor Telepon Customer')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('warning'),
                TextColumn::make('alamat')
                    ->label('Alamat Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipe_customer')
                    ->label('Tipe Customer')
                    ->icon('heroicon-o-identification')
                    ->iconColor('secondary')
                    ->searchable(),
            ])
            ->defaultSort('id_customer', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_customer')
                ->label('Tipe Customer')
                ->options([
                    'guest' => 'Guest',
                    'membership' => 'Membership',
                ])
                ->native(false)
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make('view_data')
                        ->label('Lihat Data') // Label for button
                        ->icon('heroicon-o-eye'), // Eye icon)
                    Tables\Actions\Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading(fn(Customer $record) => "Edit Customer: {$record->nama_customer}")
                        ->modalDescription('Silakan ubah data customer sesuai kebutuhan')
                        ->modalSubmitActionLabel('Simpan Perubahan')
                        ->modalCancelActionLabel('Batal')
                        ->modalWidth('md')
                        ->form([
                            TextInput::make('nama_customer')
                                ->label('Nama Customer')
                                ->required()
                                ->maxLength(255),

                            PhoneInput::make('no_telp')
                                ->label('Nomor Telepon')
                                ->required()
                                ->onlyCountries(['ID', 'SG', 'MY', 'TH'])
                                ->defaultCountry('ID'),

                            Textarea::make('alamat')
                                ->label('Alamat')
                                ->required(),

                            Select::make('tipe_customer')
                                ->options([
                                    'guest' => 'Guest',
                                    'membership' => 'Membership',
                                ])
                                ->required()
                                ->native(false),
                        ]),
                    Tables\Actions\Action::make('hapus')
                        ->label('Hapus') // Label tombol
                        ->color('danger') // Warna tombol
                        ->icon('heroicon-s-trash') // Ikon tombol
                        ->requiresConfirmation() // Tambahkan dialog konfirmasi
                        ->modalHeading('Konfirmasi Hapus') // Judul dialog
                        ->modalSubheading(fn(Customer $record) => "Apakah kamu yakin ingin menghapus {$record->nama_customer} ?") // Pesan tambahan dengan bold dan space)
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($record) {
                            $recipient = auth()->user();
                            Notification::make()
                                ->title("Customer berhasil dihapus.")
                                ->success()
                                ->send()
                                ->sendToDatabase($recipient);
                            $record->delete(); // Aksi penghapusan
                        })
                        ->action(function (Customer $record, array $data) {
                            $record->update($data);
                            $recipient = auth()->user();
                            Notification::make()
                                ->title('Customer berhasil diupdate')
                                ->success()
                                ->sendToDatabase($recipient);
                        })
                ])
            ])
            // ... (opsional: tambahkan requiresConfirmation jika perlu)
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Data') // Opsional: Menyesuaikan label
                    ->icon('heroicon-o-trash')
                    ->color('danger') // Warna merah untuk indikasi penghapusan
                    ->requiresConfirmation()
                    ->modalDescription(fn(Customer $record) => "Apakah kamu yakin ingin menghapus semua data customer ?")
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
