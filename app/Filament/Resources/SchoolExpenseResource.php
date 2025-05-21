<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolExpenseResource\Pages;
use App\Filament\Resources\SchoolExpenseResource\RelationManagers;
use App\Models\SchoolExpense;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;
use Illuminate\Database\Eloquent\Model;

class SchoolExpenseResource extends Resource
{
    protected static ?string $model = SchoolExpense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'Tuition Fee';


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
                            ->label('Applied')
                            ->inline(false)
                            ->columnSpanFull()
                            ->default(true)
                            ->onIcon('heroicon-s-check')
                            ->onColor('success')
                            ->offIcon('heroicon-s-x-mark')
                            ->required(),
                    ]),

                Section::make(__('School Fees & Miscellaneous'))
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
                                fn(array $state): ?string => strtoupper($state['fee_name'])
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
                                        fn(Action $action) => $action
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
            ->defaultSort('created_at', 'desc')
            ->queryStringIdentifier('school_expenses')
            ->recordUrl(fn(Model $record): string => route(
                'filament.app.resources.school-expenses.view',
                ['record' => $record]
            ))
            ->columns([
                Tables\Columns\TextColumn::make('expense_name')
                    ->label('Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('effectivity_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Applied')
                    ->alignCenter()
                    ->boolean(),

                Tables\Columns\TextColumn::make('sum_fees')
                    ->label('Total fees')
                    ->money('PHP')
                    ->alignEnd()
                    ->getStateUsing(fn($record): float => $record->fees->sum('fee_amount')),

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


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(7)
            ->schema([
                Fieldset::make('School Fees')
                    ->hiddenLabel()
                    ->columnSpan(['lg' => 2, 'xs' => 7, 'default' => 7])
                    ->schema([
                        TextEntry::make('expense_name')
                            ->label('Title')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->columnSpanFull(),

                        TextEntry::make('effectivity_date')
                            ->date()
                            ->columnSpanFull(),

                        IconEntry::make('is_active')
                            ->boolean()
                            ->label('Applied'),
                    ]),

                Group::make()
                    ->hiddenLabel()
                    ->columnSpan(['lg' => 5, 'xs' => 7, 'default' => 7])
                    ->schema([
                        Fieldset::make('School Fees')
                            ->hiddenLabel()
                            ->columnSpan(5)
                            ->schema([
                                TextEntry::make('sum_fees')
                                    ->columnSpan(5)
                                    ->label('Total Fees')
                                    ->money('PHP')
                                    ->inlineLabel()
                                    ->extraAttributes(['class' => 'font-bold'])
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->alignEnd()
                                    ->getStateUsing(fn($record): float => $record->fees->sum('fee_amount')),
                            ]),
                        TableRepeatableEntry::make('fees')
                            ->label('Tuition and Miscellaneous Fees')
                            ->columnSpan(5)
                            ->columns(4)
                            ->schema([
                                TextEntry::make('fee_name')
                                    ->formatStateUsing(fn($record): string => ucfirst($record->fee_name))
                                    ->label('Particulars'),


                                TextEntry::make('feeCategory.category_name')
                                    ->formatStateUsing(fn($record): string => $record->feeCategory->category_name)
                                    ->label('Category'),

                                TextEntry::make('fee_amount')
                                    ->numeric(2)
                                    ->alignEnd()
                                    ->label('Amount'),


                            ]),

                    ])

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
            'view' => Pages\ViewSchoolTuition::route('/{record}'),
        ];
    }


}
