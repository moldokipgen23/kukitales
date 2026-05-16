<?php
namespace App\Filament\Resources\FolktaleResource\Pages;

use App\Filament\Resources\FolktaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFolktale extends EditRecord
{
    protected static string $resource = FolktaleResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
