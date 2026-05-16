<?php
namespace App\Filament\Resources\FolktaleResource\Pages;

use App\Filament\Resources\FolktaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFolktale extends CreateRecord
{
    protected static string $resource = FolktaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = FolktaleResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
