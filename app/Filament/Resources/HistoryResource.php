<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class HistoryResource extends PostResource
{
    public const POST_TYPE = 'history';

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'History Articles';
    protected static ?string $navigationGroup = '🏛 History';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'History Articles';
    protected static ?string $modelLabel = 'History Articles';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorys::route('/'),
            'create' => Pages\CreateHistory::route('/create'),
            'edit' => Pages\EditHistory::route('/{record}/edit'),
        ];
    }
}
