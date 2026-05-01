<?php

namespace App\Filament\Organizer\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentTicketsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $company = $user->company;

        return $table
            ->query(
                Ticket::where('company_id', $company->id)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('رقم التذكرة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendee_name')
                    ->label('اسم الحاضر')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('الحدث')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإصدار')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'checked_in',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc');
    }
}
