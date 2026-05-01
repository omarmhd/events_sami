<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'الأحداث';
    protected static ?string $label = 'الحدث';
    protected static ?string $pluralLabel = 'الأحداث';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الحدث الأساسية')
                    ->description('أدخل التفاصيل الأساسية للحدث')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الحدث')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('event_type')
                            ->label('نوع الحدث')
                            ->options([
                                'conference' => 'مؤتمر',
                                'meeting' => 'اجتماع',
                                'workshop' => 'ورشة عمل',
                                'social' => 'حدث اجتماعي',
                                'training' => 'تدريب',
                            ])
                            ->required(),

                        Forms\Components\Select::make('registration_mode')
                            ->label('طريقة التسجيل')
                            ->options([
                                'open' => 'مفتوح',
                                'invitation_only' => 'بدعوة فقط',
                                'closed' => 'مغلق',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('التفاصيل الزمنية')
                    ->description('حدد موعد الحدث')
                    ->schema([
                        Forms\Components\DateTimePickerInput::make('start_datetime')
                            ->label('تاريخ البدء والوقت')
                            ->required(),

                        Forms\Components\DateTimePickerInput::make('end_datetime')
                            ->label('تاريخ الإنهاء والوقت')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('الموقع والسعة')
                    ->description('معلومات الموقع والسعة')
                    ->schema([
                        Forms\Components\TextInput::make('location_name')
                            ->label('اسم الموقع')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\UrlInput::make('google_map_url')
                            ->label('رابط Google Maps'),

                        Forms\Components\TextInput::make('capacity')
                            ->label('السعة')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('الإعدادات')
                    ->description('إعدادات إضافية للحدث')
                    ->schema([
                        Forms\Components\Toggle::make('requires_manual_approval')
                            ->label('يتطلب موافقة يدوية')
                            ->helperText('يتم قبول التسجيلات يدويًا فقط'),

                        Forms\Components\Toggle::make('allow_reentry')
                            ->label('السماح بإعادة الدخول')
                            ->helperText('السماح للحاضرين بدخول الحدث أكثر من مرة'),

                        Forms\Components\TextInput::make('event_slug')
                            ->label('معرّف الحدث (Slug)')
                            ->maxLength(255)
                            ->helperText('يُستخدم في الرابط'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الحدث')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->label('البدء')
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
                    ->label('النوع')
                    ->colors([
                        'primary' => 'conference',
                        'success' => 'meeting',
                        'warning' => 'workshop',
                        'info' => 'social',
                        'secondary' => 'training',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'conference' => 'مؤتمر',
                        'meeting' => 'اجتماع',
                        'workshop' => 'ورشة عمل',
                        'social' => 'حدث اجتماعي',
                        'training' => 'تدريب',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('نوع الحدث')
                    ->options([
                        'conference' => 'مؤتمر',
                        'meeting' => 'اجتماع',
                        'workshop' => 'ورشة عمل',
                        'social' => 'حدث اجتماعي',
                        'training' => 'تدريب',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        return Event::where('company_id', $user->company_id)->count();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('company_id', $user->company_id);
    }
}
