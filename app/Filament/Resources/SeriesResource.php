<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Models\Category;
use App\Models\Series;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Episode Series';
    protected static ?string $navigationGroup = '📖 Stories';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\FileUpload::make('cover_image')->image()->directory('series'),
            Forms\Components\Select::make('user_id')->label('Author')
                ->options(fn () => User::pluck('name', 'id'))->required(),
            Forms\Components\Select::make('category_id')
                ->options(fn () => Category::where('type', 'episode')->pluck('name', 'id')),
            Forms\Components\Select::make('status')->options([
                'ongoing' => 'Ongoing', 'completed' => 'Completed', 'hiatus' => 'On Hiatus',
            ])->default('ongoing'),
            Forms\Components\Toggle::make('is_featured'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\BadgeColumn::make('status'),
            Tables\Columns\TextColumn::make('user.name')->label('Author'),
            Tables\Columns\TextColumn::make('episode_count')->label('Episodes')->state(fn ($record) => $record->posts()->count()),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
