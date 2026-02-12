<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kematians\Pages;

use App\Filament\Resources\Kematians\KematianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKematian extends CreateRecord
{
    protected static string $resource = KematianResource::class;
}
