<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollaborateHereResource\Pages;
use App\Filament\Resources\CollaborateHereResource\RelationManagers;
use App\Models\CollaborateHere;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollaborateHereResource extends Resource
{
    protected static ?string $model = CollaborateHere::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('icon')
                    ->image()
                    ->directory('collaborate_icons')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('desc')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Kolaborasi')
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
                    ->label('Judul Kolaborasi')
                    ->options(fn () => \App\Models\CollaborateHere::query()->pluck('title', 'title')->toArray()),
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
            'index' => Pages\ListCollaborateHeres::route('/'),
            'create' => Pages\CreateCollaborateHere::route('/create'),
            'edit' => Pages\EditCollaborateHere::route('/{record}/edit'),
        ];
    }
}
