<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = '⚙ Settings';
    protected static ?int $navigationSort = 9;
    protected static ?string $label = 'Site Setting';
    protected static ?string $navigationLabel = 'Raw Settings (advanced)';

    public static function shouldRegisterNavigation(): bool
    {
        // Hidden by default — use the friendlier "Site Configuration" page instead.
        // Set this to true if you need direct access to every key/value pair.
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->disabledOn('edit'),
            Forms\Components\Textarea::make('value')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('value')->limit(80)->wrap(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
