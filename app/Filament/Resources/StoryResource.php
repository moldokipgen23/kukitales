<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoryResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class StoryResource extends PostResource
{
    public const POST_TYPE = 'story';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Short Stories';
    protected static ?string $navigationGroup = '📖 Stories';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Short Stories';
    protected static ?string $modelLabel = 'Story';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStories::route('/'),
            'create' => Pages\CreateStory::route('/create'),
            'edit' => Pages\EditStory::route('/{record}/edit'),
        ];
    }
}
