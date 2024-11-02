<?php

namespace App\Filament\Resources\TestmoniaResource\Pages;

use App\Filament\Resources\TestmoniaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTestmonias extends ManageRecords
{
    protected static string $resource = TestmoniaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
