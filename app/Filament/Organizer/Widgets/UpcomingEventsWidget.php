<?php

namespace App\Filament\Organizer\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingEventsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $company = $user->company;

        return $table
            ->query(
                Event::where('company_id', $company->id)
                    ->where('start_datetime', '>=', now())
                    ->orderBy('start_datetime', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الحدث')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->label('تاريخ البدء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location_name')
                    ->label('الموقع')
                    ->searchable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('السعة')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('event_type')
                    ->label('نوع الحدث')
                    ->colors([
                        'primary' => 'conference',
                        'success' => 'meeting',
                        'warning' => 'workshop',
                        'info' => 'social',
                    ]),
            ])
            ->defaultPaginationPageOption(5)
            ->defaultSort('start_datetime', 'asc');
    }
}
