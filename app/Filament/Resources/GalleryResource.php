<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class GalleryResource extends PostResource
{
    public const POST_TYPE = 'gallery';

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Gallery';
    protected static ?string $navigationGroup = '🎵 Culture';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Gallery items';
    protected static ?string $modelLabel = 'Gallery';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGallerys::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
