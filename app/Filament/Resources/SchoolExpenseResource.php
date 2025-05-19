<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolExpenseResource\Pages;
use App\Filament\Resources\SchoolExpenseResource\RelationManagers;
use App\Models\SchoolExpense;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SchoolExpenseResource extends Resource
{
    protected static ?string $model = SchoolExpense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('expense_name')
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->label('Title')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('effectivity_date')
                            ->columnSpanFull()
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->inline(false)
                            ->columnSpanFull()
                            ->default(true)
                            ->onIcon('heroicon-s-check')
                            ->onColor('success')
                            ->required(),
                    ]),

                Section::make(__('List of School Fees'))
                    ->columnSpan(5)
                    ->schema([
                        Forms\Components\Repeater::make('fees')
                            ->hiddenLabel()
                            ->columns(4)
                            ->relationship()
                            ->collapsible()
                            ->minItems(1)
                            ->addActionLabel('Add Fee')
                            ->itemLabel(
                                fn (array $state): ?string => strtoupper($state['fee_name'])
                            )
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\TextInput::make('fee_name')
                                    ->live()
                                    ->label('Name')
                                    ->required(),

                                Forms\Components\TextInput::make('fee_amount')
                                    ->label('Amount')
                                    ->numeric(2)
                                    ->required(),

                                Forms\Components\Select::make('fee_type')
                                    ->options([
                                        'mandatory' => 'Mandatory',
                                        'optional' => 'Optional',
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('fee_category_id')
                                    ->label('Category')
                                    ->relationship('feeCategory', 'category_name')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('category_name')
                                            ->required(),
                                    ])
                                    ->createOptionAction(
                                        fn (Action $action) => $action
                                            ->label('Create Department')
                                            ->modalSubmitActionLabel('Submit')
                                            ->modalHeading('New Category')
                                            ->modalWidth('md')
                                            ->modalFooterActionsAlignment('end')
                                            ->closeModalByClickingAway(false)
                                    )
                                    ->required(),

                            ]),
                    ])


            ])
            ->columns(7);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('effectivity_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                //
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
            'index' => Pages\ListSchoolExpenses::route('/'),
            'create' => Pages\CreateSchoolExpense::route('/create'),
            'edit' => Pages\EditSchoolExpense::route('/{record}/edit'),
        ];
    }
}
