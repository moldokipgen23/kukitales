<?php
namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = NewsResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
