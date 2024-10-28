<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VariantResource\Pages;
use App\Filament\Resources\VariantResource\RelationManagers;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantResource extends Resource
{
    protected static ?string $model = Variant::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?int $navigationSort = 3;
    public static function getNavigationLabel(): string
    {
        return __('Products Features');
    }

    public static function getModelLabel(): string
    {
        return __('Products');
    }
    public static function getNavigationGroup(): string
    {
        return __('the Products');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Product feature');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('attribute_id')
                ->relationship('attribute','name')
                ->label('attribute')
                ->preload()
                ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute.name')
                ->numeric()
                ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListVariants::route('/'),
            'create' => Pages\CreateVariant::route('/create'),
            'edit' => Pages\EditVariant::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
