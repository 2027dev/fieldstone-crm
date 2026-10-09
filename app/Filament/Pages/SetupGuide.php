<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\ContactResource;
use App\Filament\Resources\DealResource;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use Filament\Pages\Page;

class SetupGuide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Setup Guide';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.setup-guide';

    public function getTasks(): array
    {
        return [
            [
                'title' => 'Add a contact',
                'description' => 'Enter details about a person or company so their deals and activities can be linked.',
                'time' => '1-2 min',
                'done' => Contact::count() > 0,
                'url' => ContactResource::getUrl(),
                'cta' => 'Add contact',
            ],
            [
                'title' => 'Schedule an activity',
                'description' => 'Arrange the details of a call, meeting or task to advance a deal.',
                'time' => '1-2 min',
                'done' => Activity::count() > 0,
                'url' => ActivityResource::getUrl(),
                'cta' => 'Schedule activity',
            ],
            [
                'title' => 'Add a deal',
                'description' => 'Create an opportunity to move it through your sales process and close faster.',
                'time' => '2-4 min',
                'done' => Deal::count() > 0,
                'url' => DealResource::getUrl(),
                'cta' => 'Add deal',
            ],
        ];
    }

    public function getCompletedCount(): int
    {
        return collect($this->getTasks())->where('done', true)->count();
    }
}
