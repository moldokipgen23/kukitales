<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Category;
use App\Models\Post;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'All Posts';
    protected static ?string $navigationGroup = '🛡 Moderation';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->tabs([

                Forms\Components\Tabs\Tab::make('Content')->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(255)->live(onBlur: true),
                    Forms\Components\Select::make('type')
                        ->options([
                            'story' => 'Story', 'episode' => 'Episode', 'history' => 'History',
                            'folktale' => 'Folktale', 'news' => 'News', 'blog' => 'Blog',
                            'music' => 'Music', 'gallery' => 'Gallery',
                        ])->required()->live(),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('series_id')
                        ->label('Series')
                        ->options(fn () => Series::orderBy('title')->pluck('title', 'id'))
                        ->searchable()
                        ->visible(fn ($get) => $get('type') === 'episode'),
                    Forms\Components\TextInput::make('episode_number')
                        ->numeric()
                        ->visible(fn ($get) => $get('type') === 'episode'),
                    Forms\Components\Textarea::make('excerpt')->rows(2)->maxLength(500),
                    Forms\Components\RichEditor::make('content')->required()->columnSpanFull(),
                ]),

                Forms\Components\Tabs\Tab::make('Media')->schema([
                    Forms\Components\FileUpload::make('cover_image')->image()->directory('covers')->visibility('public'),
                    Forms\Components\FileUpload::make('audio_file')->directory('audio')->visibility('public'),
                ]),

                Forms\Components\Tabs\Tab::make('Settings')->schema([
                    Forms\Components\Select::make('status')->options([
                        'draft' => 'Draft', 'pending' => 'Pending Review',
                        'published' => 'Published', 'rejected' => 'Rejected',
                    ])->default('draft')->required(),
                    Forms\Components\Toggle::make('is_featured'),
                    Forms\Components\Toggle::make('is_breaking')->visible(fn ($get) => $get('type') === 'news'),
                    Forms\Components\Toggle::make('allow_tts')->default(true),
                    Forms\Components\TextInput::make('year_era')->visible(fn ($get) => $get('type') === 'history'),
                    Forms\Components\TextInput::make('source'),
                    Forms\Components\DateTimePicker::make('published_at'),
                ]),

                Forms\Components\Tabs\Tab::make('SEO')->schema([
                    Forms\Components\Placeholder::make('seo_hint')
                        ->label('')
                        ->content('Leave fields blank to auto-generate from title/content. Focus keyword helps you stay on-topic.'),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('SEO Title')
                        ->maxLength(70)
                        ->helperText('Appears in Google results & browser tab. Keep under 60 characters. Defaults to post title.'),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->rows(3)
                        ->maxLength(160)
                        ->helperText('Shown under the title in Google results. 150-160 characters ideal.'),
                    Forms\Components\TextInput::make('focus_keyword')
                        ->label('Focus Keyword')
                        ->maxLength(120)
                        ->helperText('The main keyword/phrase you want this post to rank for.'),
                    Forms\Components\FileUpload::make('og_image')
                        ->label('Share Image (Open Graph)')
                        ->image()
                        ->directory('og-images')
                        ->helperText('Shown when shared on Facebook, WhatsApp, Twitter. 1200×630px recommended. Falls back to cover image.'),
                    Forms\Components\TextInput::make('canonical_url')
                        ->label('Canonical URL')
                        ->url()
                        ->helperText('Only set if this post is a copy of one published elsewhere. Otherwise leave blank.'),
                    Forms\Components\Toggle::make('noindex')
                        ->label('Hide from search engines')
                        ->helperText('Excludes this post from sitemap and adds noindex meta tag.'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\BadgeColumn::make('type')->colors([
                    'warning' => 'story', 'primary' => 'news', 'success' => 'folktale',
                    'info' => 'episode', 'gray' => 'history', 'danger' => 'blog',
                ]),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'gray' => 'draft', 'warning' => 'pending',
                    'success' => 'published', 'danger' => 'rejected',
                ]),
                Tables\Columns\TextColumn::make('user.name')->label('Author')->toggleable(),
                Tables\Columns\TextColumn::make('view_count')->sortable()->label('Views'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'story' => 'Story', 'episode' => 'Episode', 'history' => 'History',
                    'folktale' => 'Folktale', 'news' => 'News', 'blog' => 'Blog',
                    'music' => 'Music', 'gallery' => 'Gallery',
                ]),
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'pending' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected',
                ]),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
