<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FolktaleResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class FolktaleResource extends PostResource
{
    public const POST_TYPE = 'folktale';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Folktales';
    protected static ?string $navigationGroup = '📖 Stories';
    protected static ?int $navigationSort = 4;
    protected static ?string $pluralModelLabel = 'Folktales';
    protected static ?string $modelLabel = 'Folktales';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFolktales::route('/'),
            'create' => Pages\CreateFolktale::route('/create'),
            'edit' => Pages\EditFolktale::route('/{record}/edit'),
        ];
    }
}
