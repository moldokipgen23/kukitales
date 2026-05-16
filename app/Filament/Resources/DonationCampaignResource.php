<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationCampaignResource\Pages;
use App\Models\DonationCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DonationCampaignResource extends Resource
{
    protected static ?string $model = DonationCampaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Donation Campaigns';
    protected static ?string $navigationGroup = '💰 Monetization';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Campaign';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Campaign')
                ->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(150),
                    Forms\Components\TextInput::make('beneficiary')->helperText('Who or what this raises funds for.'),
                    Forms\Components\Textarea::make('short_description')->rows(2)->maxLength(280),
                    Forms\Components\RichEditor::make('description')->columnSpanFull(),
                    Forms\Components\FileUpload::make('cover_image')->image()->directory('donations'),
                ])->columns(2),

            Forms\Components\Section::make('Goal & Currency')
                ->schema([
                    Forms\Components\TextInput::make('goal_amount')->numeric()->prefix('₹')->helperText('Leave blank for no goal — just an open donate page.'),
                    Forms\Components\Select::make('currency')->options([
                        'INR' => 'INR (₹)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)', 'GBP' => 'GBP (£)',
                    ])->default('INR'),
                    Forms\Components\TextInput::make('raised_amount')->numeric()->prefix('₹')->disabled()->dehydrated(false)->helperText('Auto-calculated from confirmed donations.'),
                ])->columns(3),

            Forms\Components\Section::make('Visibility & Schedule')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Toggle::make('is_featured')->helperText('Pinned to homepage donate section.'),
                    Forms\Components\DateTimePicker::make('starts_at'),
                    Forms\Components\DateTimePicker::make('ends_at'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
            Tables\Columns\TextColumn::make('progress_percent')->label('Progress')->state(fn ($record) => $record->progress_percent . '%'),
            Tables\Columns\TextColumn::make('raised_amount')->money(fn ($record) => $record->currency),
            Tables\Columns\TextColumn::make('goal_amount')->money(fn ($record) => $record->currency)->label('Goal')->placeholder('—'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('is_featured', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonationCampaigns::route('/'),
            'create' => Pages\CreateDonationCampaign::route('/create'),
            'edit' => Pages\EditDonationCampaign::route('/{record}/edit'),
        ];
    }
}
