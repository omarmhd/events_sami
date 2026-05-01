<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\InvitationResource\Pages;
use App\Models\EventInvitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InvitationResource extends Resource
{
    protected static ?string $model = EventInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'الدعوات';
    protected static ?string $label = 'الدعوة';
    protected static ?string $pluralLabel = 'الدعوات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الدعوة')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('الحدث')
                            ->relationship('event', 'name')
                            ->required(),

                        Forms\Components\TextInput::make('invited_email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('invited_name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('invited_phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('حالة الدعوة')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'معلقة',
                                'accepted' => 'مقبولة',
                                'rejected' => 'مرفوضة',
                                'cancelled' => 'ملغاة',
                            ])
                            ->required(),

                        Forms\Components\DateTimePickerInput::make('responded_at')
                            ->label('تاريخ الرد')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invited_email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invited_name')
                    ->label('الاسم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('الحدث')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'accepted',
                        'warning' => 'pending',
                        'danger' => 'rejected',
                        'secondary' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'accepted' => 'مقبولة',
                        'pending' => 'معلقة',
                        'rejected' => 'مرفوضة',
                        'cancelled' => 'ملغاة',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلقة',
                        'accepted' => 'مقبولة',
                        'rejected' => 'مرفوضة',
                        'cancelled' => 'ملغاة',
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'view' => Pages\ViewInvitation::route('/{record}'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        return EventInvitation::where('company_id', $user->company_id)
            ->where('status', 'pending')
            ->count();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('company_id', $user->company_id);
    }
}
