<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Forms\Components\PostalCode;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->required()
                    ->tel()
                    ->maxLength(20),
                Section::make('Endereço')
                ->schema([
                    PostalCode::make('zipcode')
                            ->label('CEP')
                            ->viaCep(
                                setFields: [
                                'street' => 'logradouro', 
                                'neighborhood' => 'bairro', 
                                'state' => 'uf', 
                                'city' => 'localidade',
                                'complement' => 'complemento',
                                ]
                            ),
                    TextInput::make('state')->label('Estado')->maxLength(255),
                    TextInput::make('city')->label('Cidade')->maxLength(255),
                    TextInput::make('street')->label('Rua')->maxLength(255),
                    TextInput::make('number')->label('Número')->maxLength(10),
                    TextInput::make('neighborhood')->label('Bairro')->maxLength(255),
                    TextInput::make('complement')->label('Complemento')->maxLength(255),
                ])->collapsible()->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Endereços')
            ->recordTitleAttribute('street')
            ->columns([
                TextColumn::make('name')
                    ->label('Endereço'),
                TextColumn::make('phone')
                    ->label('Telefone'),
                TextColumn::make('city')
                    ->label('Cidade'),
                TextColumn::make('state')
                    ->label('Estado'),
                TextColumn::make('zipcode')
                    ->label('Cep'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
}
