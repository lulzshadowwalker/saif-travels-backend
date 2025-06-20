<?php

namespace App\Filament\Resources;

use App\Enums\SupportStatus;
use App\Filament\Resources\SupportResource\Pages;
use App\Models\Support;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportResource extends Resource
{
    protected static ?string $model = Support::class;

    protected static ?string $navigationIcon = "heroicon-o-chat-bubble-left-right";
    protected static ?string $navigationGroup = "Support";
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = "Support Request";
    protected static ?string $pluralModelLabel = "Support Requests";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Contact Information")
                ->description("Customer contact details")
                ->aside()
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Name")
                        ->placeholder("Enter customer name")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make("email")
                        ->label("Email")
                        ->placeholder("Enter customer email")
                        ->email()
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make("phone")
                        ->label("Phone")
                        ->placeholder("Enter customer phone number")
                        ->tel()
                        ->required()
                        ->maxLength(50),

                    Forms\Components\Select::make("status")
                        ->label("Status")
                        ->options(SupportStatus::class)
                        ->required()
                        ->default(SupportStatus::open->value)
                        ->searchable()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Name")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make("email")
                    ->label("Email")
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage("Email address copied")
                    ->copyMessageDuration(1500)
                    ->icon("heroicon-m-envelope"),

                Tables\Columns\TextColumn::make("phone")
                    ->label("Phone")
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage("Phone number copied")
                    ->copyMessageDuration(1500)
                    ->icon("heroicon-m-phone"),

                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make("created_at")
                    ->label("Submitted At")
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make("updated_at")
                    ->label("Updated At")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->label("Status")
                    ->options(SupportStatus::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make("created_at")
                    ->form([
                        Forms\Components\DatePicker::make(
                            "created_from"
                        )->label("Created from"),
                        Forms\Components\DatePicker::make(
                            "created_until"
                        )->label("Created until"),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data["created_from"],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    "created_at",
                                    ">=",
                                    $date
                                )
                            )
                            ->when(
                                $data["created_until"],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    "created_at",
                                    "<=",
                                    $date
                                )
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data["created_from"] ?? null) {
                            $indicators["created_from"] =
                                "Created from " .
                                \Carbon\Carbon::parse(
                                    $data["created_from"]
                                )->format("M j, Y");
                        }

                        if ($data["created_until"] ?? null) {
                            $indicators["created_until"] =
                                "Created until " .
                                \Carbon\Carbon::parse(
                                    $data["created_until"]
                                )->format("M j, Y");
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make("markAsResolved")
                        ->label("Mark as resolved")
                        ->icon("heroicon-o-check-circle")
                        ->color("success")
                        ->action(function ($records): void {
                            $records->each(function ($record) {
                                $record->update([
                                    "status" => SupportStatus::resolved,
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make("markAsClosed")
                        ->label("Mark as closed")
                        ->icon("heroicon-o-x-circle")
                        ->color("danger")
                        ->action(function ($records): void {
                            $records->each(function ($record) {
                                $record->update([
                                    "status" => SupportStatus::closed,
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort("created_at", "desc")
            ->poll("30s");
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListSupports::route("/"),
            "create" => Pages\CreateSupport::route("/create"),
            "edit" => Pages\EditSupport::route("/{record}/edit"),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()
            ::where("status", SupportStatus::open)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()
            ::where("status", SupportStatus::open)
            ->count() > 5
            ? "danger"
            : "primary";
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}
