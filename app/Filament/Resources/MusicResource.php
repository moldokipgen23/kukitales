<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MusicResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class MusicResource extends PostResource
{
    public const POST_TYPE = 'music';

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';
    protected static ?string $navigationLabel = 'Music & Songs';
    protected static ?string $navigationGroup = '🎵 Culture';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Music & Songs';
    protected static ?string $modelLabel = 'Music & Songs';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMusics::route('/'),
            'create' => Pages\CreateMusic::route('/create'),
            'edit' => Pages\EditMusic::route('/{record}/edit'),
        ];
    }
}
