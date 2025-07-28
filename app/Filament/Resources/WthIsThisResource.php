<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WthIsThisResource\Pages;
use App\Filament\Resources\WthIsThisResource\RelationManagers;
use App\Models\WthIsThis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WthIsThisResource extends Resource
{
    protected static ?string $model = WthIsThis::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('desc')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('img')
                    ->image()
                    ->directory('wthis_images'),
                Forms\Components\Textarea::make('short_desc')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->maxLength(255),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Sub Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desc')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('title')
                    ->label('Judul')
                    ->options(fn () => \App\Models\WthIsThis::query()->pluck('title', 'title')->toArray()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListWthIsThis::route('/'),
            'create' => Pages\CreateWthIsThis::route('/create'),
            'edit' => Pages\EditWthIsThis::route('/{record}/edit'),
        ];
    }
}
