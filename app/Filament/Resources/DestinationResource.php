<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = "heroicon-o-map-pin";
    protected static ?string $navigationGroup = "Travel";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Destination Details")
                ->description("Manage destination information and media")
                ->aside()
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Name")
                        ->placeholder("Enter destination name")
                        ->helperText("This field supports multiple languages.")
                        ->required()
                        ->maxLength(255)
                        ->translatable(),

                    Forms\Components\SpatieMediaLibraryFileUpload::make(
                        "images"
                    )
                        ->label("Images")
                        ->helperText(
                            "Upload images for this destination. You can upload multiple images."
                        )
                        ->collection(Destination::MEDIA_COLLECTION_IMAGES)
                        ->multiple()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(["16:9", "4:3", "1:1"])
                        ->maxSize(10240) // 10MB
                        ->acceptedFileTypes([
                            "image/jpeg",
                            "image/png",
                            "image/webp",
                        ])
                        ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make("packages_count")
                    ->label("Packages")
                    ->counts("packages")
                    ->sortable()
                    ->badge(),

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
                //
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
            "index" => Pages\ListDestinations::route("/"),
            "create" => Pages\CreateDestination::route("/create"),
            "edit" => Pages\EditDestination::route("/{record}/edit"),
        ];
    }
}
