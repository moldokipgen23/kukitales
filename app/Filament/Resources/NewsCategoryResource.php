<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsCategoryResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class NewsCategoryResource extends CategoryResource
{
    public const CATEGORY_TYPE = 'news';

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'News Categories';
    protected static ?string $navigationGroup = '📰 News';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'News Categories';
    protected static ?string $modelLabel = 'News Category';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::CATEGORY_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsCategories::route('/'),
            'create' => Pages\CreateNewsCategory::route('/create'),
            'edit' => Pages\EditNewsCategory::route('/{record}/edit'),
        ];
    }
}
