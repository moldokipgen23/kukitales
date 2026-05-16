<?php
namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStory extends CreateRecord
{
    protected static string $resource = StoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = StoryResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
