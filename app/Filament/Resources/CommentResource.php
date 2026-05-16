<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = '🛡 Moderation';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('content')->rows(4)->required(),
            Forms\Components\Select::make('status')->options([
                'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Author'),
            Tables\Columns\TextColumn::make('post.title')->label('On')->limit(40),
            Tables\Columns\TextColumn::make('content')->limit(60)->wrap(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected',
            ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected',
            ]),
        ])->actions([
            Action::make('approve')->color('success')->icon('heroicon-o-check')
                ->visible(fn (Comment $r) => $r->status !== 'approved')
                ->action(fn (Comment $r) => $r->update(['status' => 'approved'])),
            Action::make('reject')->color('danger')->icon('heroicon-o-x-mark')
                ->visible(fn (Comment $r) => $r->status !== 'rejected')
                ->action(fn (Comment $r) => $r->update(['status' => 'rejected'])),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkAction::make('approve_selected')
                ->action(fn ($records) => $records->each->update(['status' => 'approved']))
                ->color('success'),
            Tables\Actions\BulkAction::make('reject_selected')
                ->action(fn ($records) => $records->each->update(['status' => 'rejected']))
                ->color('danger'),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
