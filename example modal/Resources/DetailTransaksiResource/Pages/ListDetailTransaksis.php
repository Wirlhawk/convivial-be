<?php

namespace App\Filament\Resources\DetailTransaksiResource\Pages;

use App\Filament\Resources\DetailTransaksiResource;
use App\Models\DetailTransaksi;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListDetailTransaksis extends ListRecords
{
    protected static ?string $breadcrumb = "List Detail Transaksi";

    public function getTabs(): array
{
    return [
        'All' => Tab::make()
            ->label('Semua')
            ->badge(fn() => DetailTransaksi::count() ?: null)
            ->modifyQueryUsing(fn(Builder $query) => $query),

        'Hari Ini' => Tab::make()
            ->label('Hari Ini')
            ->badge(fn() => DetailTransaksi::whereDate('created_at', today())->count() ?: null)
            ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', today())),

        'Minggu Ini' => Tab::make()
            ->label('Minggu Ini')
            ->badge(fn() => DetailTransaksi::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count() ?: null)
            ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])),

        'Bulan Ini' => Tab::make()
            ->label('Bulan Ini')
            ->badge(fn() => DetailTransaksi::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])->count() ?: null)
            ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])),
    ];
}
    protected static string $resource = DetailTransaksiResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
