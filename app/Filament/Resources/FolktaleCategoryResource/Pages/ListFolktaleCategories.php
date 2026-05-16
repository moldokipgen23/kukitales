<?php
namespace App\Filament\Resources\FolktaleCategoryResource\Pages;

use App\Filament\Resources\FolktaleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFolktaleCategories extends ListRecords
{
    protected static string $resource = FolktaleCategoryResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
