<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FolktaleCategoryResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class FolktaleCategoryResource extends CategoryResource
{
    public const CATEGORY_TYPE = 'folktale';

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Folktale Categories';
    protected static ?string $navigationGroup = '📖 Stories';
    protected static ?int $navigationSort = 5;
    protected static ?string $pluralModelLabel = 'Folktale Categories';
    protected static ?string $modelLabel = 'Folktale Category';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::CATEGORY_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFolktaleCategories::route('/'),
            'create' => Pages\CreateFolktaleCategory::route('/create'),
            'edit' => Pages\EditFolktaleCategory::route('/{record}/edit'),
        ];
    }
}
