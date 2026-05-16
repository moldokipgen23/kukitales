<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Advertisements';
    protected static ?string $navigationGroup = '💰 Monetization';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Advertisements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ad Details')
                ->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(150)->helperText('Internal label only — not shown on the site.'),
                    Forms\Components\Select::make('type')
                        ->required()
                        ->live()
                        ->options([
                            'banner_image' => '🖼  Banner Image',
                            'video' => '🎬 Video (YouTube / Vimeo / mp4)',
                            'custom_html' => '⌨  Custom HTML (AdSense, network tag, iframe)',
                        ]),
                    Forms\Components\Select::make('placement')
                        ->required()
                        ->options([
                            'homepage_hero' => 'Homepage — under hero',
                            'homepage_middle' => 'Homepage — between sections',
                            'homepage_bottom' => 'Homepage — above footer',
                            'sidebar' => 'History page sidebar',
                            'article_top' => 'Article — top (under TTS bar)',
                            'article_middle' => 'Article — middle (after first paragraph)',
                            'article_bottom' => 'Article — bottom (above comments)',
                            'category_top' => 'Category page — top',
                        ])
                        ->helperText('Where on the public site this ad will appear.'),
                    Forms\Components\TextInput::make('label')
                        ->maxLength(40)
                        ->placeholder('Sponsored')
                        ->helperText('Small disclosure label shown above the ad (e.g. "Sponsored", "Promotion").'),
                ])->columns(2),

            Forms\Components\Section::make('Content')
                ->schema([
                    Forms\Components\FileUpload::make('image_path')
                        ->image()
                        ->directory('ads')
                        ->visibility('public')
                        ->visible(fn ($get) => $get('type') === 'banner_image')
                        ->helperText('Recommended: 1200×300 for full-width banners, 600×500 for sidebar.'),
                    Forms\Components\TextInput::make('video_url')
                        ->visible(fn ($get) => $get('type') === 'video')
                        ->placeholder('https://youtu.be/... or https://vimeo.com/... or https://your-cdn.com/video.mp4')
                        ->helperText('YouTube / Vimeo URLs are auto-embedded. Direct mp4 also works.'),
                    Forms\Components\Textarea::make('custom_html')
                        ->rows(6)
                        ->visible(fn ($get) => $get('type') === 'custom_html')
                        ->helperText('Paste an iframe, an AdSense snippet, or any HTML. Trusted source — rendered as-is.'),
                    Forms\Components\TextInput::make('link_url')
                        ->url()
                        ->visible(fn ($get) => $get('type') !== 'custom_html')
                        ->helperText('Where users go when they click the ad. Click count tracked.'),
                    Forms\Components\TextInput::make('alt_text')
                        ->visible(fn ($get) => $get('type') === 'banner_image')
                        ->helperText('Accessibility text for the image.'),
                ]),

            Forms\Components\Section::make('Schedule & Visibility')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\TextInput::make('priority')->numeric()->default(0)->helperText('Higher priority shows first when multiple ads target the same placement.'),
                    Forms\Components\DateTimePicker::make('starts_at')->helperText('Leave blank to start immediately.'),
                    Forms\Components\DateTimePicker::make('ends_at')->helperText('Leave blank for no end date.'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
            Tables\Columns\BadgeColumn::make('type')->colors([
                'success' => 'banner_image',
                'warning' => 'video',
                'gray' => 'custom_html',
            ]),
            Tables\Columns\TextColumn::make('placement')->badge(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('view_count')->label('Views')->sortable(),
            Tables\Columns\TextColumn::make('click_count')->label('Clicks')->sortable(),
            Tables\Columns\TextColumn::make('ends_at')->dateTime()->label('Expires')->toggleable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('placement')->options([
                'homepage_hero' => 'Homepage hero', 'homepage_middle' => 'Homepage middle', 'homepage_bottom' => 'Homepage bottom',
                'sidebar' => 'Sidebar', 'article_top' => 'Article top', 'article_middle' => 'Article middle', 'article_bottom' => 'Article bottom',
                'category_top' => 'Category top',
            ]),
            Tables\Filters\TernaryFilter::make('is_active'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit' => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
