<?php
namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEpisode extends CreateRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = EpisodeResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
