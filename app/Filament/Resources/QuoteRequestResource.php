<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages\CreateQuoteRequest;
use App\Filament\Resources\QuoteRequestResource\Pages\EditQuoteRequest;
use App\Filament\Resources\QuoteRequestResource\Pages\ListQuoteRequests;
use App\Filament\Resources\QuoteRequestResource\Pages\ViewQuoteRequest;
use App\Models\QuoteRequest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Árajánlat kérések';

    protected static ?string $modelLabel = 'Árajánlat kérés';

    protected static ?string $pluralModelLabel = 'Árajánlat kérések';

    protected static ?string $navigationGroup = 'Ügyfélszolgálat';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kapcsolattartó adatok')
                    ->schema([
                        TextInput::make('name')
                            ->label('Név')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telefonszám')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('company')
                            ->label('Cég (opcionális)')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email cím')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make('Kérés részletei')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Tárgy')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label('Üzenet')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Kezelés')
                    ->schema([
                        Select::make('status')
                            ->label('Állapot')
                            ->options([
                                'new' => 'Új',
                                'contacted' => 'Kapcsolatfelvéve',
                                'closed' => 'Lezárva',
                            ])
                            ->required(),

                        DateTimePicker::make('contacted_at')
                            ->label('Kapcsolatfelvétel időpontja')
                            ->seconds(false),

                        Textarea::make('notes')
                            ->label('Megjegyzések')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Állapot')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Új',
                        'contacted' => 'Kapcsolatfelvéve',
                        'closed' => 'Lezárva',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'new',
                        'warning' => 'contacted',
                        'success' => 'closed',
                    ])
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('subject')
                    ->label('Tárgy')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('contacted_at')
                    ->label('Kapcsolatfelvéve')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Állapot')
                    ->options([
                        'new' => 'Új',
                        'contacted' => 'Kapcsolatfelvéve',
                        'closed' => 'Lezárva',
                    ]),
            ])
            ->actions([
                Action::make('contact')
                    ->label('Kapcsolatfelvéve')
                    ->icon('heroicon-o-phone')
                    ->color('warning')
                    ->visible(fn (QuoteRequest $record): bool => $record->status === 'new')
                    ->action(function (QuoteRequest $record): void {
                        $record->update([
                            'status' => 'contacted',
                            'contacted_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Állapot frissítve')
                            ->body('Az árajánlat kérés állapota "Kapcsolatfelvéve"-re változott.')
                            ->success()
                            ->send();
                    }),

                Action::make('close')
                    ->label('Lezárás')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (QuoteRequest $record): bool => $record->status !== 'closed')
                    ->action(function (QuoteRequest $record): void {
                        $record->update([
                            'status' => 'closed',
                        ]);

                        Notification::make()
                            ->title('Kérés lezárva')
                            ->body('Az árajánlat kérés sikeresen lezárva.')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'create' => CreateQuoteRequest::route('/create'),
            'view' => ViewQuoteRequest::route('/{record}'),
            'edit' => EditQuoteRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = self::getModel()::where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return self::getModel()::where('status', 'new')->count() > 0 ? 'danger' : null;
    }
}
