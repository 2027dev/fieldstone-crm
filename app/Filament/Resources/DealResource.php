<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealResource\Pages;
use App\Models\Deal;
use App\Models\DealStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 4;

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
                Forms\Components\Select::make('deal_stage_id')
                    ->label('Stage')
                    ->relationship('stage', 'name')
                    ->default(fn () => DealStage::orderBy('sort_order')->value('id'))
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ])
                    ->default('open')
                    ->required(),
                Forms\Components\DatePicker::make('expected_close_date'),
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
                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Stage')
                    ->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'won' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expected_close_date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ]),
                Tables\Filters\SelectFilter::make('deal_stage_id')
                    ->label('Stage')
                    ->relationship('stage', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('markWon')
                    ->label('Mark won')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Deal $record) => $record->status === 'open')
                    ->action(function (Deal $record) {
                        $record->update(['status' => 'won']);
                        Notification::make()->title('Deal marked as won')->success()->send();
                    }),
                Tables\Actions\Action::make('markLost')
                    ->label('Mark lost')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Deal $record) => $record->status === 'open')
                    ->action(function (Deal $record) {
                        $record->update(['status' => 'lost']);
                        Notification::make()->title('Deal marked as lost')->danger()->send();
                    }),
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
            'index' => Pages\ManageDeals::route('/'),
        ];
    }
}
