<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class EpisodeResource extends PostResource
{
    public const POST_TYPE = 'episode';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Episodes';
    protected static ?string $navigationGroup = '📖 Stories';
    protected static ?int $navigationSort = 3;
    protected static ?string $pluralModelLabel = 'Episodes';
    protected static ?string $modelLabel = 'Episodes';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}
