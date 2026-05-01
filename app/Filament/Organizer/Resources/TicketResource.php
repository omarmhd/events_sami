<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'التذاكر';
    protected static ?string $label = 'التذكرة';
    protected static ?string $pluralLabel = 'التذاكر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات التذكرة')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('رقم التذكرة')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('event_id')
                            ->label('الحدث')
                            ->relationship('event', 'name')
                            ->required(),

                        Forms\Components\TextInput::make('attendee_name')
                            ->label('اسم الحاضر')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('attendee_email')
                            ->label('بريد إلكتروني')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('attendee_phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('حالة التذكرة')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'معلق',
                                'checked_in' => 'تم التسجيل',
                                'cancelled' => 'ملغ',
                            ])
                            ->required(),

                        Forms\Components\DateTimePickerInput::make('checked_in_at')
                            ->label('وقت التسجيل')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('رقم التذكرة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendee_name')
                    ->label('اسم الحاضر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendee_email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('الحدث')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'checked_in',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'checked_in' => 'تم التسجيل',
                        'pending' => 'معلق',
                        'cancelled' => 'ملغ',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلق',
                        'checked_in' => 'تم التسجيل',
                        'cancelled' => 'ملغ',
                    ]),

                Tables\Filters\SelectFilter::make('event_id')
                    ->label('الحدث')
                    ->relationship('event', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        return Ticket::where('company_id', $user->company_id)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('company_id', $user->company_id);
    }
}
