<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    
    protected static ?string $navigationGroup = 'Invoice';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Customer Name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-user'), // Add an icon here if desired
    
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-phone'),
    
                // Repeater for Invoice Items
                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        // Product Selection
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Product')
                            ->placeholder('Select Product')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    $set('price', $product ? $product->base_price : null);
                                }
                            })
                            ->columns(1)
                            ->prefixIcon('heroicon-o-paper-clip'),
    
                        // Product Price
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->readOnly()
                            ->columns(1)
                            ->prefixIcon('heroicon-o-currency-dollar'),
    
                        // Quantity Input
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minLength(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $price = $get('price') ?? 0;
                                $set('total_price', $price * $state);
                                $items = $get('../../items') ?? [];
                                $total = array_sum(array_map(fn($item) => $item['total_price'] ?? 0, $items));
                                $set('../../total', $total);
                            })
                            ->columns(1)
                            ->prefixIcon('heroicon-o-calculator'),
    
                        // Calculated field for item total price
                        TextInput::make('total_price')
                            ->label('Item Total')
                            ->numeric()
                            ->readOnly()
                            ->columns(1)
                            ->prefixIcon('heroicon-o-check-circle'),
                    ])
                    ->columns(4)  // Adjust the Repeater schema to 4 columns to align inputs in 2-per-row
                    ->columnSpanFull()
                    ->reactive()
                    ->live(onBlur: true),
    
                // Total Calculation
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->readOnly()
                    ->reactive()
                    ->prefixIcon('heroicon-o-calculator')
                    ->columnSpanFull(),


                    RichEditor::make('notes')
                    ->label('Notes')
                    ->required()->columnSpanFull(),
            ]);
    }
    
    


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
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
            ->query(Invoice::query())
            ->modifyQueryUsing(function ($query) {
                return $query->where('created_by', auth()->id()); // Ensure you're filtering by user ID
            })
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\Action::make('print')
                ->icon('heroicon-o-printer')
                ->label('Print Invoice')
                ->color('blue')
                ->action(function (Invoice $record) {
                    return redirect()->route('filament.admin.resources.invoices.print', ['invoiceId' => $record->id]);
                }),
                
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'print' => Pages\PrintInvoice::route('/print/{invoiceId}'), // Ensure you include the invoiceId parameter
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
