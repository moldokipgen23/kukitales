<?php
namespace App\Filament\Resources\HistoryResource\Pages;

use App\Filament\Resources\HistoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHistory extends CreateRecord
{
    protected static string $resource = HistoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = HistoryResource::POST_TYPE;
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        return $data;
    }
}
