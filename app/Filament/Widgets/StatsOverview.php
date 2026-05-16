<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count())
                ->description(Post::where('status', 'published')->count() . ' published')
                ->color('success')
                ->icon('heroicon-o-document-text'),
            Stat::make('Total Users', User::count())
                ->description(User::where('role', '!=', 'reader')->count() . ' staff & authors')
                ->color('info')
                ->icon('heroicon-o-users'),
            Stat::make('Total Views', Post::sum('view_count'))
                ->color('warning')
                ->icon('heroicon-o-eye'),
            Stat::make('Pending Approval', Post::where('status', 'pending')->count() + Comment::where('status', 'pending')->count())
                ->description('Posts + comments awaiting review')
                ->color('danger')
                ->icon('heroicon-o-clock'),
        ];
    }
}
