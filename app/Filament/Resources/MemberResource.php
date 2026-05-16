<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Members';
    protected static ?string $modelLabel = 'Member';
    protected static ?string $pluralModelLabel = 'Members';
    protected static ?string $navigationGroup = '👥 People';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'reader');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('bio')->rows(3),
            Forms\Components\TextInput::make('location'),
            Forms\Components\Toggle::make('is_active')->default(true)
                ->helperText('Turn off to block sign-in without deleting the account.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('location')->toggleable(),
            Tables\Columns\TextColumn::make('bookmarks_count')->counts('bookmarks')->label('Bookmarks'),
            Tables\Columns\TextColumn::make('comments_count')->counts('comments')->label('Comments'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->label('Verified')->placeholder('—'),
            Tables\Columns\TextColumn::make('created_at')->date()->label('Joined')->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_active'),
            Tables\Filters\Filter::make('verified')
                ->query(fn ($q) => $q->whereNotNull('email_verified_at'))
                ->label('Email verified'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('promote_to_author')
                ->icon('heroicon-o-arrow-up-circle')->color('warning')
                ->visible(fn (User $r) => $r->role === 'reader')
                ->requiresConfirmation()
                ->action(fn (User $r) => $r->update(['role' => 'author'])),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
