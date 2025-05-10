<?php

namespace App\Filament\Resources\GradeLevelResource\RelationManagers;

use App\Models\Section;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('section_name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->deferLoading()
            ->queryStringIdentifier('sections')
            ->recordTitleAttribute('section_name')
            ->columns([
                Tables\Columns\TextColumn::make('section_name'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Section')
                    ->color('success')
                    ->icon('heroicon-o-plus')
                    ->size('sm')
                    ->form([
                        TextInput::make('section_name')
                            ->required()
                            ->label('Section Name')
                            ->rules(function (?Section $record) {
                                // Get the parent grade_level_id from the relation manager
                                $gradeLevelId = $this->getOwnerRecord()->id ?? null;
                                return [
                                    Rule::unique('sections', 'section_name')
                                        ->where(fn ($query) => $query->where('grade_level_id', $gradeLevelId))
                                        ->ignore($record),
                                ];
                            }),
                    ])
                    ->modalHeading('Create New Section')
                    ->modalFooterActionsAlignment('end')
                    ->closeModalByClickingAway(false)
                    ->modalCancelAction(false)
                    ->modalWidth('md')
                    ->successNotification(
                        Notification::make()
                        ->success()
                        ->title('Confirmation')
                        ->body('New section successfully created.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('success')
                    ->form([
                        TextInput::make('section_name')
                            ->label('Section Name')
                            ->rules(function (?Section $record) {
                                // Get the parent grade_level_id from the relation manager
                                $gradeLevelId = $this->getOwnerRecord()->id ?? null;
                                return [
                                    Rule::unique('sections', 'section_name')
                                        ->where(fn ($query) => $query->where('grade_level_id', $gradeLevelId))
                                        ->ignore($record),
                                ];
                            })
                            ->required()
                    ])
                    ->modalHeading('Edit Section')
                    ->modalFooterActionsAlignment('end')
                    ->closeModalByClickingAway(false)
                    ->modalCancelAction(false)
                    ->modalWidth('md')
                    ->successNotification(
                        Notification::make()
                        ->success()
                        ->title('Confirmation')
                        ->body('Section successfully updated.')
                    )
                    ,
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);


    }

    public function canCreate(): bool
    {
        return true;
    }










}
