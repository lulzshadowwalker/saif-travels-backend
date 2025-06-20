<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = "heroicon-o-question-mark-circle";
    protected static ?string $navigationGroup = "Support";
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = "FAQ";
    protected static ?string $pluralModelLabel = "FAQs";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Frequently Asked Question")
                ->description("Create and manage FAQ entries")
                ->aside()
                ->schema([
                    Forms\Components\TextInput::make("question")
                        ->label("Question")
                        ->placeholder("Enter the frequently asked question")
                        ->helperText("This field supports multiple languages.")
                        ->required()
                        ->maxLength(500)
                        ->translatable()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make("answer")
                        ->label("Answer")
                        ->placeholder("Enter the answer to this question")
                        ->helperText(
                            "Provide a clear and comprehensive answer. This field supports multiple languages."
                        )
                        ->required()
                        ->rows(5)
                        ->translatable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("question")
                    ->label("Question")
                    ->searchable()
                    ->sortable()
                    ->limit(80)
                    ->tooltip(function (
                        Tables\Columns\TextColumn $column
                    ): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 80) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make("answer")
                    ->label("Answer")
                    ->searchable()
                    ->limit(120)
                    ->toggleable()
                    ->tooltip(function (
                        Tables\Columns\TextColumn $column
                    ): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 120) {
                            return null;
                        }
                        return $state;
                    }),

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
            ->defaultSort("created_at", "desc")
            ->striped();
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
            "index" => Pages\ListFaqs::route("/"),
            "create" => Pages\CreateFaq::route("/create"),
            "edit" => Pages\EditFaq::route("/{record}/edit"),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? "warning" : "primary";
    }
}
