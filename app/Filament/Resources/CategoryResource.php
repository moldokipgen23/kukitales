<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'All Categories';
    protected static ?string $navigationGroup = '⚙ Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('type')->options([
                'story' => 'Story', 'episode' => 'Episode', 'history' => 'History',
                'folktale' => 'Folktale', 'news' => 'News', 'blog' => 'Blog',
                'music' => 'Music', 'gallery' => 'Gallery',
            ])->required(),
            Forms\Components\Textarea::make('description')->rows(2),
            Forms\Components\TextInput::make('icon')->placeholder('e.g. 📖'),
            Forms\Components\ColorPicker::make('color'),
            Forms\Components\Select::make('parent_id')
                ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                ->searchable(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('icon'),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\TextColumn::make('parent.name')->label('Parent'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
