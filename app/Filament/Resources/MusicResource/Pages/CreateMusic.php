<?php
namespace App\Filament\Resources\MusicResource\Pages;

use App\Filament\Resources\MusicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMusic extends CreateRecord
{
    protected static string $resource = MusicResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = MusicResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
