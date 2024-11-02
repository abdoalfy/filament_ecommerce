<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CopounResource\Pages;
use App\Filament\Resources\CopounResource\RelationManagers;
use App\Models\Copoun;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CopounResource extends Resource
{
    protected static ?string $model = Copoun::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Products';
    
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('the Products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('copoun')
                    ->required(),
                    Forms\Components\TextInput::make('value')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')->rules([]),
                Forms\Components\DatePicker::make('ena_date'),
                Select::make('type')
                ->label('Copoun Type')
                ->options([
                    'value' => 'Value',
                    'percentage' => 'Percentage',
                ])
                ->required(),
            Select::make('status')
                ->label(__('Copoun Status'))
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->required(),
                    Forms\Components\TextInput::make('limit')
                    ->numeric()
                    ->default(1)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('copoun'),
                TextColumn::make('start_date'),
                TextColumn::make('ena_date'),
                TextColumn::make('limit')
                    ->numeric()
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state === 'value' ? 'Value' : 'Percentage')
                    ->colors([
                        'value' => 'green',
                        'percentage' => 'blue',
                    ])
                    ->icon(fn ($state) => $state === 'value' ? 'heroicon-s-currency-dollar' : 'heroicon-s-percent'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'Active' : 'Inactive')
                    ->colors([
                        'active' => 'success',
                        'inactive' => 'danger',
                    ])
                    ->icon(fn ($state) => $state === 'active' ? 'heroicon-s-check-circle' : 'heroicon-s-x-circle'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    // ->default('active') // Default filter to show "active" records
                    ->label('Filter by Status'),
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);


    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCopouns::route('/'),
        ];
    }
}
