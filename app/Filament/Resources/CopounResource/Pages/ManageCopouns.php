<?php

namespace App\Filament\Resources\CopounResource\Pages;

use App\Filament\Resources\CopounResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCopouns extends ManageRecords
{
    protected static string $resource = CopounResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
