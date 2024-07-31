<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ImagesRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $slug = 'produtos';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informações do produto')->schema([
                        TextInput::make('name')
                            ->label('Nome do Produto')
                            ->maxLength(255)
                            ->live()
                            ->required()->minLength(1)->maxLength(150)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                            if ($operation === 'edit') {
                                return;
                            }

                            $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),
                        MarkdownEditor::make('Descrição')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')
                    ])->columns(2),

                    Section::make('Imagens')->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->directory('products-gb')
                            ->maxFiles(6)
                            ->reorderable()
                    ])

                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Valores')->schema([
                        TextInput::make('price')
                            ->label('Valor')
                            ->numeric()
                            ->required()
                            ->prefix('R$')
                        
                    ]),
                    Section::make()->schema([
                        Select::make('category_id')
                            ->label('Categoria')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('Category', 'name'),

                        Select::make('brand_id')
                            ->label('Marca')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('Brand', 'name')
                    ]),
                    Section::make('Status')->schema([
                        Toggle::make('in_stock')
                            ->label('em estoque')
                            ->required()
                            ->default(true),
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->required()
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('destaque')
                            ->required(),
                        Toggle::make('on_sale')
                            ->label('à venda')
                            ->required(),
                    ])
                ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Produto')->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->label('Marca')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Valor')
                    ->money('R$'),
                IconColumn::make('is_featured')->label('Destaque')->boolean(),
                IconColumn::make('on_sale')->label('Venda')->boolean(),
                IconColumn::make('in_stock')->label('Estoque')->boolean(),
                IconColumn::make('is_active')->label('Status')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')->label('Categoria')->relationship('category', 'name'),
                SelectFilter::make('brand')->label('Marca')->relationship('brand', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

    public static function getModelLabel(): string
    {
        return __('Produto');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Produtos');
    }
}
