<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $openValue = Deal::where('status', 'open')->sum('value');
        $wonThisMonth = Deal::where('status', 'won')->whereMonth('updated_at', now()->month)->sum('value');

        return [
            Stat::make('Open pipeline value', '$'.number_format($openValue))
                ->description(Deal::where('status', 'open')->count().' open deals')
                ->color('success'),
            Stat::make('Won this month', '$'.number_format($wonThisMonth))
                ->color('success'),
            Stat::make('Contacts', Contact::count())
                ->description('People in your CRM'),
            Stat::make('Open activities', Activity::where('done', false)->count())
                ->description('To-dos remaining'),
            Stat::make('Leads inbox', Lead::where('status', 'inbox')->count())
                ->description('Awaiting qualification'),
        ];
    }
}
