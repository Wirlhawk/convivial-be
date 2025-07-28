<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-circle';
    protected static ?int $navigationSort = -1;
    protected static ?string $pluralLabel = 'Akun'; // Override nama plural
    protected static ?string $label = 'Akun Petugas'; // Override nama singular
    protected static ?string $navigationLabel = 'Akun Petugas';
    protected static ?string $slug = 'account/petugas';
    protected static ?string $navigationGroup = 'Manajemen Akun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->collapsible()
                    ->description('Masukkan detail user yang akan dibuat')
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('primary')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nama User'),
                        TextInput::make('email')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Email User'),
                        TextInput::make('password')
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof CreateUser)
                            ->rule(RulesPassword::min(8)->letters()->mixedCase()->numbers()->symbols())
                            ->label('Password'),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->label('Role')
                            ->required()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(1),

                Section::make('User New Password')
                    ->collapsible()
                    ->description('Masukkan password baru jika ingin mengubah password')
                    ->icon('heroicon-m-lock-closed')
                    ->iconColor('warning')
                    ->schema([
                        TextInput::make('new_password')
                            ->nullable()
                            ->password()
                            ->rule(RulesPassword::min(8)->letters()->mixedCase()->numbers()->symbols())
                            ->label('Password Baru'),
                        TextInput::make('new_password_confirmation')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password')
                            ->label('Konfirmasi Password Baru'),
                    ])
                    ->visible(fn($livewire) => $livewire instanceof EditUser)
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama User')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email User')
                    ->icon('heroicon-o-envelope')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('warning')
                    ->label('Jabatan')
                    ->sortable()
                    ->formatStateUsing(fn($state): string => is_array($state) ? implode(', ', $state) : ($state ?? 'Tidak Ada')),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->icon('heroicon-o-calendar')
                    ->dateTime('d F Y')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Jabatan')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([ // ActionGroup updated with ellipsis icon
                    Tables\Actions\ViewAction::make('view_data')
                        ->label('Lihat Data') // Label for button
                        ->icon('heroicon-o-eye'), // Eye icon)

                    Tables\Actions\Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading(fn(User $record) => "Edit Petugas: {$record->name}")
                        ->modalDescription('Silakan ubah data jenis cucian sesuai kebutuhan')
                        ->modalSubmitActionLabel('Simpan Perubahan')
                        ->modalCancelActionLabel('Batal')
                        ->modalWidth('md')
                        ->form([
                            Section::make('User Details')
                                ->collapsible()
                                ->description('Masukkan detail user yang akan dibuat')
                                ->icon('heroicon-m-user-circle')
                                ->iconColor('primary')
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->label('Nama User'),
                                    TextInput::make('email')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->label('Email User'),
                                    TextInput::make('password')
                                        ->required()
                                        ->password()
                                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                        ->visible(fn($livewire) => $livewire instanceof CreateUser)
                                        ->rule(RulesPassword::min(8)->letters()->mixedCase()->numbers()->symbols())
                                        ->label('Password'),
                                    Select::make('roles')
                                        ->relationship('roles', 'name')
                                        ->label('Role')
                                        ->required()
                                        ->preload()
                                        ->searchable(),
                                ])
                                ->columns(1),

                            Section::make('User New Password')
                                ->collapsible()
                                ->description('Masukkan password baru jika ingin mengubah password')
                                ->icon('heroicon-m-lock-closed')
                                ->iconColor('warning')
                                ->schema([
                                    TextInput::make('new_password')
                                        ->nullable()
                                        ->password()
                                        ->rule(RulesPassword::min(8)->letters()->mixedCase()->numbers()->symbols())
                                        ->label('Password Baru'),
                                    TextInput::make('new_password_confirmation')
                                        ->password()
                                        ->same('new_password')
                                        ->requiredWith('new_password')
                                        ->label('Konfirmasi Password Baru'),
                                ])
                                ->visible(fn($livewire) => $livewire instanceof EditUser)
                                ->columns(1),
                        ])
                        ->action(function (User $record, array $data) {
                            $record->update($data);
                            $recipient = auth()->user();
                            Notification::make()
                                ->title('Data Akun berhasil diupdate')
                                ->success()
                                ->sendToDatabase($recipient);
                        }),
                    Tables\Actions\Action::make('hapus')
                        ->label('Hapus') // Label tombol
                        ->color('danger') // Warna tombol
                        ->icon('heroicon-s-trash') // Ikon tombol
                        ->requiresConfirmation() // Tambahkan dialog konfirmasi
                        ->modalHeading('Konfirmasi Hapus') // Judul dialog
                        ->modalSubheading(fn(User $record) => "Apakah kamu yakin ingin menghapus akun yang bernama {$record->name} ?") // Pesan tambahan dengan bold dan space)
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($record) {
                            $recipient = auth()->user();
                            Notification::make()
                                ->title("Transaksi berhasil dihapus.")
                                ->success()
                                ->send()
                                ->sendToDatabase($recipient);
                            $record->delete(); // Aksi penghapusan
                        }),
                ])->label('Aksi') // Set group label
                    ->icon('heroicon-o-ellipsis-vertical')
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Data') // Opsional: Menyesuaikan label
                    ->icon('heroicon-o-trash')
                    ->color('danger') // Warna merah untuk indikasi penghapusan
                    ->requiresConfirmation()
                    ->modalDescription(fn(User $record) => "Apakah kamu yakin ingin menghapus semua data akun ?")
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
