<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationLabel = 'Categorias';

    protected static ?string $slug = 'categorias';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        FileUpload::make('image')
                            ->label('Capa')
                            ->disk('public')
                            ->visibility('private')
                            ->directory(env('AWS_PASTA') . 'categories')
                            ->image()->imageEditor(), 
                    ])->columnSpan(1),

                Section::make()
                    ->schema([
                        TextInput::make('name')
                                ->label('Nome da Categoria')
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
                    ->unique(Category::class, 'slug', ignoreRecord: true),
                Toggle::make('is_active')
                    ->label('Status')
                    ->required()
                    ->default(true),
                    ])->columnSpan(2)->columns(2),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Capa'),
                TextColumn::make('name')->label('Categoria')->searchable(),  
                TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y')->sortable(),
                ToggleColumn::make('is_active')->searchable()->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Categoria');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Categorias');
    }
}
