<?php
namespace App\Filament\Resources\FolktaleCategoryResource\Pages;

use App\Filament\Resources\FolktaleCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFolktaleCategory extends CreateRecord
{
    protected static string $resource = FolktaleCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = FolktaleCategoryResource::CATEGORY_TYPE;
        return $data;
    }
}
