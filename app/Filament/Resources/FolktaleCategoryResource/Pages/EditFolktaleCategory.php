<?php
namespace App\Filament\Resources\FolktaleCategoryResource\Pages;

use App\Filament\Resources\FolktaleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFolktaleCategory extends EditRecord
{
    protected static string $resource = FolktaleCategoryResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
