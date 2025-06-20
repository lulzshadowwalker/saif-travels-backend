<?php

namespace App\Filament\Resources;

use App\Enums\PackageStatus;
use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = "heroicon-o-cube";
    protected static ?string $navigationGroup = "Travel";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Package Information")
                ->description("Basic package details and settings")
                ->aside()
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Package Name")
                        ->placeholder("Enter package name")
                        ->helperText("This field supports multiple languages.")
                        ->required()
                        ->maxLength(255)
                        ->translatable()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            string $operation,
                            $state,
                            Forms\Set $set
                        ) {
                            if ($operation === "create" && $state) {
                                $set(
                                    "slug",
                                    \Illuminate\Support\Str::slug($state)
                                );
                            }
                        }),

                    Forms\Components\Select::make("status")
                        ->label("Status")
                        ->options(PackageStatus::class)
                        ->required()
                        ->default(PackageStatus::active->value)
                        ->searchable(),

                    Forms\Components\TextInput::make("durations")
                        ->label("Duration")
                        ->suffix("days")
                        ->placeholder("Enter duration in days")
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(365),

                    Forms\Components\TextInput::make("tags")
                        ->label("Tags")
                        ->placeholder("Enter tags separated by commas")
                        ->helperText("Use commas to separate multiple tags")
                        ->maxLength(500),

                    Forms\Components\Select::make("destinations")
                        ->label("Destinations")
                        ->relationship("destinations", "name")
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText(
                            "Select one or more destinations for this package"
                        )
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make("Package Content")
                ->description("Detailed information about the package")
                ->aside()
                ->schema([
                    Forms\Components\Textarea::make("description")
                        ->label("Description")
                        ->placeholder("Enter package description")
                        ->helperText("This field supports multiple languages.")
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make("chips")
                        ->label("Key Features (Chips)")
                        ->placeholder("Enter key features/highlights")
                        ->helperText(
                            "Brief highlights or features. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make("goal")
                        ->label("Goal")
                        ->placeholder("Enter the goal of this package")
                        ->helperText(
                            "What this package aims to achieve. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make("program")
                        ->label("Program Details")
                        ->placeholder("Enter detailed program information")
                        ->helperText(
                            "Detailed itinerary or program. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make("activities")
                        ->label("Activities")
                        ->placeholder(
                            "Enter activities included in the package"
                        )
                        ->helperText(
                            "List of activities. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make("stay")
                        ->label("Accommodation Details")
                        ->placeholder("Enter accommodation information")
                        ->helperText(
                            "Details about where guests will stay. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make("iv_drips")
                        ->label("IV Drips Information")
                        ->placeholder("Enter IV drips details")
                        ->helperText(
                            "Information about IV drips included. This field supports multiple languages."
                        )
                        ->required()
                        ->translatable()
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make("Media")
                ->description("Package images and media files")
                ->aside()
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make(
                        "images"
                    )
                        ->label("Package Images")
                        ->helperText(
                            "Upload images for this package. You can upload multiple images."
                        )
                        ->collection(Package::MEDIA_COLLECTION_IMAGES)
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
                        ->columnSpanFull()
                        ->reorderable(),
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

                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make("durations")
                    ->label("Duration")
                    ->numeric()
                    ->sortable()
                    ->suffix(" days")
                    ->badge(),

                Tables\Columns\TextColumn::make("destinations.name")
                    ->label("Destinations")
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(2)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make("tags")
                    ->label("Tags")
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->options(PackageStatus::class),

                Tables\Filters\SelectFilter::make("destinations")
                    ->label("Destinations")
                    ->relationship("destinations", "name")
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
            "index" => Pages\ListPackages::route("/"),
            "create" => Pages\CreatePackage::route("/create"),
            "edit" => Pages\EditPackage::route("/{record}/edit"),
        ];
    }
}
