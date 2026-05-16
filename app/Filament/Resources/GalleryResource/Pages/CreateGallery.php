<?php
namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = GalleryResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
