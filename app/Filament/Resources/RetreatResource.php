<?php

namespace App\Filament\Resources;

use App\Enums\RetreatStatus;
use App\Filament\Resources\RetreatResource\Pages;
use App\Filament\Resources\RetreatResource\RelationManagers;
use App\Models\Retreat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RetreatResource extends Resource
{
    protected static ?string $model = Retreat::class;

    protected static ?string $navigationIcon = "heroicon-o-rectangle-group";
    protected static ?string $navigationGroup = "Travel";
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Retreat Information")
                ->description("Basic retreat details and settings")
                ->aside()
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Retreat Name")
                        ->placeholder("Enter retreat name")
                        ->helperText("This field supports multiple languages.")
                        ->required()
                        ->maxLength(255)
                        ->translatable(),

                    Forms\Components\Select::make("status")
                        ->label("Status")
                        ->options(RetreatStatus::class)
                        ->required()
                        ->default(RetreatStatus::active->value)
                        ->searchable(),
                ]),

            Forms\Components\Section::make("Packages")
                ->description("Select packages to include in this retreat")
                ->aside()
                ->schema([
                    Forms\Components\Select::make("packages")
                        ->label("Packages")
                        ->relationship("packages", "name")
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText(
                            "Select one or more packages to include in this retreat"
                        )
                        ->columnSpanFull()
                        ->getSearchResultsUsing(
                            fn(string $search) => \App\Models\Package::where(
                                "name",
                                "like",
                                "%{$search}%"
                            )
                                ->orWhere("name->en", "like", "%{$search}%")
                                ->orWhere("name->ar", "like", "%{$search}%")
                                ->limit(50)
                                ->pluck("name", "id")
                        )
                        ->getOptionLabelsUsing(
                            fn(
                                array $values
                            ): array => \App\Models\Package::whereIn(
                                "id",
                                $values
                            )
                                ->pluck("name", "id")
                                ->toArray()
                        ),
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
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make("packages.name")
                    ->label("Packages")
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make("created_at")
                    ->label("Created At")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make("updated_at")
                    ->label("Updated At")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->label("Status")
                    ->options(RetreatStatus::class),

                Tables\Filters\SelectFilter::make("packages")
                    ->label("Has Package")
                    ->relationship("packages", "name")
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort("created_at", "desc");
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
            "index" => Pages\ListRetreats::route("/"),
            "create" => Pages\CreateRetreat::route("/create"),
            "edit" => Pages\EditRetreat::route("/{record}/edit"),
        ];
    }
}
