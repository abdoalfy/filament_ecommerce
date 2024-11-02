<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\Concerns\Translatable;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    use Translatable;
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    public static function getNavigationLabel(): string
    {
        return __('Products');
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
        return __('Products');
    }


    protected static ?string $navigationGroup = 'Products';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->schema([
                        // Step 1: Base Information
                        Wizard\Step::make('Base Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Product Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->dehydrated()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Product Slug')
                                    ->required()
                                    ->readOnly(),

                                Forms\Components\TextInput::make('base_price')
                                    ->label('Base Price')
                                    ->required()
                                    ->numeric(),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\TextInput::make('points')
                                    ->label('points')
                                    ->required()
                                    ->numeric(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ]),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Description')
                                    ->columnSpanFull(),
                            ]),

                        // Step 2: Product Images
                        Wizard\Step::make('Product Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('main_image')
                                    ->label('Main Image')
                                    ->image()
                                    ->directory('uploads/products/main_images')
                                    ->required(),

                                Repeater::make('images')
                                    ->label('Product Images')
                                    ->relationship('images') // Link to images relationship
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Upload Image')
                                            ->directory('uploads/products/sub_images')
                                            ->disk('public')
                                            ->required(),
                                    ]),
                            ]),

                        // Step 3: Product Variants and Attributes
                        Wizard\Step::make('Product Variants')
                        ->icon('heroicon-o-cube')
                        ->schema([
                            Repeater::make('attributeProducts')
                                ->label('Product Attributes')
                                ->relationship('attributeProducts')  // Link to productAttributes relationship
                                ->schema([
                                    // Select the attribute first
                                    Select::make('attribute_id')
                                        ->label('Attribute')
                                        ->required()
                                        ->options(Attribute::query()->pluck('name', 'id'))
                                        ->reactive() // Make it reactive to display attributes
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            // Fetch variants based on the selected attribute
                                            $variants = Variant::where('attribute_id', $state)->pluck('name', 'id');
                                            $set('variant_id_options', $variants);
                                            $set('variant_id', null); // Reset attribute selection
                                        }),
                    
                                    // Select the attribute based on the chosen variant
                                    Select::make('variant_id')
                                        ->label('Variants')
                                        ->required()
                                        ->options(function ($get) {
                                            // Get the available variants set in the previous field's callback
                                            return $get('variant_id_options') ?: [];
                                        })
                                        ->searchable()
                                        ->preload(),
                    
                                    Forms\Components\TextInput::make('extra_price')
                                        ->label('Extra Price')
                                        ->numeric(),

                                    TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->minLength(1)
                                    ->validationMessages([
                                        'required'=> __('this fieled is requierd 1')
                                    ])
                                    ->label('avialbel quantity'),
                    
                                    // Repeater for attribute-specific images
                                    Repeater::make('attributeProductImages')
                                        ->label('Variant Images')
                                        ->relationship('attributeProductImages')  // Link to productAttributeImages relationship
                                        ->schema([
                                            Forms\Components\FileUpload::make('image')
                                                ->directory('uploads/products/variant_images')
                                                ->label('Upload Image')
                                                ->disk('public')
                                                ->required(),
                                        ]),
                                ]),
                        ]),
                    ])->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->label(__('name')),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label(__('Product Slug')),
                Tables\Columns\ImageColumn::make('main_image')
                ->size(60)
                ->label(__('Product main image'))
                ->circular(),
                Tables\Columns\TextColumn::make('base_price')
                    ->numeric()
                    ->sortable()
                    ->label(__('main price')),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable()
                    ->label(__('product category')),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable()
                    ->label(__('points')),
                Tables\Columns\TextColumn::make('status')->label(__('status')),
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
