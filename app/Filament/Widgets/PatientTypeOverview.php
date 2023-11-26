<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use App\Models\Cafe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PatientTypeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Cafes', Cafe::query()->count()),
            Stat::make('Diaries', Diary::query()->count()),
        ];
    }
}
