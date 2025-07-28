<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailTransaksiResource\Pages;
use App\Filament\Resources\DetailTransaksiResource\RelationManagers;
use App\Models\DetailTransaksi;
use Filament\Forms;
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

class DetailTransaksiResource extends Resource
{
    protected static ?string $model = DetailTransaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-bag';
    protected static ?string $pluralLabel = 'Detail Transaksi';
    protected static ?string $label = 'Detail Transaksi';
    protected static ?string $navigationLabel = 'Detail Transaksi';
    protected static ?string $slug = 'kasir/detail-transaksi';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_transaksi')
                    ->relationship('transaksi', 'id_transaksi')
                    ->label('ID Transaksi')
                    ->required()
                    ->native(false),
                
                Select::make('id_jenis_cucian')
                    ->relationship('jenisCucian', 'nama_jenis_cucian')
                    ->label('Jenis Cucian')
                    ->required()
                    ->native(false),
                
                TextInput::make('berat')
                    ->label('Berat (kg)')
                    ->numeric()
                    ->required()
                    ->minValue(0.1)
                    ->step(0.1),
                
                TextInput::make('sub_total')
                    ->label('Sub Total')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_detail_transaksi')
                    ->label('ID Detail Transaksi')
                    ->sortable()
                    ->iconColor('primary-light')
                    ->searchable()
                    ->icon('heroicon-o-hashtag'),
                
                TextColumn::make('transaksi.id_transaksi')
                    ->label('ID Transaksi')
                    ->sortable()
                    ->searchable()
                    ->iconColor('primary-light')
                    ->icon('heroicon-o-document-text'),
                
                TextColumn::make('jenisCucian.nama_jenis_cucian')
                    ->label('Jenis Cucian')
                    ->sortable()
                    ->searchable()
                    ->iconColor('warning')
                    ->icon('heroicon-o-tag'),
                
                TextColumn::make('berat')
                    ->label('Berat (kg)')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => number_format($state, 1) . ' kg')
                    ->icon('heroicon-o-scale'),
                
                TextColumn::make('sub_total')
                    ->label('Sub Total')
                    ->sortable()
                    ->searchable()
                    ->iconColor('success')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->icon('heroicon-o-currency-dollar'),
                
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->sortable()
                    ->dateTime('d F Y H:i')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary'),
            ])
            ->defaultSort('id_detail_transaksi', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading(fn(DetailTransaksi $record) => "Edit Detail Transaksi: {$record->id_detail_transaksi}")
                        ->modalDescription('Silakan ubah data detail transaksi sesuai kebutuhan')
                        ->modalSubmitActionLabel('Simpan Perubahan')
                        ->modalCancelActionLabel('Batal')
                        ->modalWidth('md')
                        ->form([
                            Select::make('id_transaksi')
                                ->relationship('transaksi', 'id_transaksi')
                                ->required(),
                            
                            Select::make('id_jenis_cucian')
                                ->relationship('jenisCucian', 'nama_jenis_cucian')
                                ->required(),
                            
                            TextInput::make('berat')
                                ->numeric()
                                ->required(),
                            
                            TextInput::make('sub_total')
                                ->numeric()
                                ->required(),
                        ])
                        ->action(function (DetailTransaksi $record, array $data) {
                            $record->update($data);
                            $recipient = auth()->user();
                            Notification::make()
                                ->title('Detail Transaksi berhasil diupdate')
                                ->success()
                                ->sendToDatabase($recipient);
                        }),
                    
                    Tables\Actions\Action::make('hapus')
                        ->label('Hapus')
                        ->color('danger')
                        ->icon('heroicon-s-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Hapus')
                        ->modalSubheading(fn(DetailTransaksi $record) => "Apakah kamu yakin ingin menghapus detail transaksi {$record->id_detail_transaksi}?")
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($record) {
                            $recipient = auth()->user();
                            Notification::make()
                                ->title("Detail Transaksi berhasil dihapus.")
                                ->success()
                                ->sendToDatabase($recipient);
                            $record->delete();
                        })
                ])
                ->label('Aksi')
                ->icon('heroicon-o-ellipsis-vertical')
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Data')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Apakah kamu yakin ingin menghapus semua data detail transaksi terpilih?')
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
            'index' => Pages\ListDetailTransaksis::route('/'),
            'create' => Pages\CreateDetailTransaksi::route('/create'),
            'edit' => Pages\EditDetailTransaksi::route('/{record}/edit'),
        ];
    }
}