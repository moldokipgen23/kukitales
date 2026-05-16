<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class BlogResource extends PostResource
{
    public const POST_TYPE = 'blog';

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Blog Posts';
    protected static ?string $navigationGroup = '✍️ Blog';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Blog Posts';
    protected static ?string $modelLabel = 'Blog Posts';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', static::POST_TYPE);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
