<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(Activity::TYPES)
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('due_date'),
                Forms\Components\Select::make('priority')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                    ]),
                Forms\Components\Select::make('contact_id')
                    ->label('Contact person')
                    ->relationship('contact', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('deal_id')
                    ->label('Deal')
                    ->relationship('deal', 'title')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('outcome')
                    ->maxLength(255),
                Forms\Components\Toggle::make('done'),
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
                Tables\Columns\IconColumn::make('done')
                    ->boolean()
                    ->label('Done'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Activity::TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact person'),
                Tables\Columns\TextColumn::make('deal.title')
                    ->label('Deal'),
                Tables\Columns\TextColumn::make('outcome'),
            ])
            ->defaultSort('due_date')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Activity::TYPES),
                Tables\Filters\TernaryFilter::make('done'),
                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\Select::make('period')
                            ->options([
                                'overdue' => 'Overdue',
                                'today' => 'Today',
                                'tomorrow' => 'Tomorrow',
                                'this_week' => 'This week',
                                'next_week' => 'Next week',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['period'] ?? null) {
                            'overdue' => $query->whereDate('due_date', '<', today())->where('done', false),
                            'today' => $query->whereDate('due_date', today()),
                            'tomorrow' => $query->whereDate('due_date', today()->addDay()),
                            'this_week' => $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]),
                            'next_week' => $query->whereBetween('due_date', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleDone')
                    ->label(fn (Activity $record) => $record->done ? 'Mark as to-do' : 'Mark done')
                    ->icon(fn (Activity $record) => $record->done ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-check')
                    ->color(fn (Activity $record) => $record->done ? 'gray' : 'success')
                    ->action(fn (Activity $record) => $record->update(['done' => ! $record->done])),
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
            'index' => Pages\ManageActivities::route('/'),
        ];
    }
}
