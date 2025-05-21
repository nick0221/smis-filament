<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StudentStatus;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentStatusResource\Pages;
use App\Filament\Resources\StudentStatusResource\RelationManagers;

class StudentStatusResource extends Resource
{
    protected static ?string $model = StudentStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('label')
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $normalized = ucwords(strtolower($state));
                            $set('label', $normalized); // Update label with ucwords
                            $set('key', str()->slug($normalized, '_')); // Update key based on formatted label
                        }
                    })

                    ->required(),

                Forms\Components\TextInput::make('key')
                    ->readOnly()
                    ->required(),

                Forms\Components\Select::make('color')
                    ->required()
                    ->options([
                        'red' => 'Red',
                        'yellow' => 'Yellow',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'rose' => 'Rose',
                        'cyan' => 'Cyan',
                        'orange' => 'Orange',
                        'emerald' => 'Emerald',
                        'pink' => 'Pink',
                        'violet' => 'Violet',

                    ]),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->columns([

                Tables\Columns\TextColumn::make('label')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('color')
                    ->badge()
                    ->color(fn ($record): string => $record->color)
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
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
                TrashedFilter::make(),

           ])
            ->actions([
                Tables\Actions\EditAction::make(),


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
            'index' => Pages\ListStudentStatuses::route('/'),
            'create' => Pages\CreateStudentStatus::route('/create'),
            'edit' => Pages\EditStudentStatus::route('/{record}/edit'),
        ];
    }
}
