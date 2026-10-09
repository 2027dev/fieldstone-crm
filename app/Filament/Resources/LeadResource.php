<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Leads Inbox';

    protected static ?string $pluralModelLabel = 'Leads';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('contact_id')
                    ->label('Contact')
                    ->relationship('contact', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('source')
                    ->options([
                        'Website' => 'Website',
                        'Referral' => 'Referral',
                        'LinkedIn' => 'LinkedIn',
                        'Cold Call' => 'Cold Call',
                        'Trade Show' => 'Trade Show',
                    ]),
                Forms\Components\Select::make('owner_id')
                    ->label('Owner')
                    ->relationship('owner', 'name')
                    ->default(fn () => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'converted' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization'),
                Tables\Columns\TextColumn::make('value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'inbox' => 'Inbox',
                        'converted' => 'Converted',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'Website' => 'Website',
                        'Referral' => 'Referral',
                        'LinkedIn' => 'LinkedIn',
                        'Cold Call' => 'Cold Call',
                        'Trade Show' => 'Trade Show',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('convertToDeal')
                    ->label('Convert to deal')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('success')
                    ->visible(fn (Lead $record) => $record->status === 'inbox')
                    ->action(function (Lead $record) {
                        $deal = Deal::create([
                            'title' => $record->title,
                            'contact_id' => $record->contact_id,
                            'organization_id' => $record->organization_id,
                            'deal_stage_id' => DealStage::orderBy('sort_order')->value('id'),
                            'value' => $record->value ?? 0,
                            'owner_id' => $record->owner_id,
                        ]);

                        $record->update([
                            'status' => 'converted',
                            'converted_deal_id' => $deal->id,
                        ]);

                        Notification::make()->title('Lead converted to a deal')->success()->send();
                    }),
                Tables\Actions\Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->visible(fn (Lead $record) => $record->status === 'inbox')
                    ->action(fn (Lead $record) => $record->update(['status' => 'archived'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeads::route('/'),
        ];
    }
}
