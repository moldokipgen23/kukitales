<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class NewsResource extends PostResource
{
    public const POST_TYPE = 'news';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'News Posts';
    protected static ?string $navigationGroup = '📰 News';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'News Posts';
    protected static ?string $modelLabel = 'News Posts';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewss::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
