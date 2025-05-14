<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Filament\Resources\ClassRoomResource\RelationManagers;
use App\Models\ClassRoom;
use App\Models\GradeLevel;
use App\Models\Section;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {


        return $form
            ->schema([
                // Grade Level Selection
                Select::make('grade_level_id')
                    ->live()
                    ->relationship('gradeLevel', 'grade_name')
                    ->preload()
                    ->searchable(['grade_name'])
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('section_id', null); // Reset section when grade changes
                        $grade = $get('grade_level_id');
                        $section = $get('section_id');
                        if ($grade && $section) {
                            $gradeName = GradeLevel::find($grade)?->grade_name;
                            $sectionName = Section::find($section)?->section_name;
                            $set('room_name', "{$gradeName} - {$sectionName}");
                        }
                    }),


                // Grade Level Selection
                Select::make('section_id')
                    ->label('Section')
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $grade = $get('grade_level_id');
                        $section = $get('section_id');
                        if ($grade && $section) {
                            $gradeName = GradeLevel::find($grade)?->grade_name;
                            $sectionName = Section::find($section)?->section_name;
                            $set('room_name', "{$gradeName} - {$sectionName}");
                        }
                    })
                    ->hintAction(
                        Action::make('create_one')
                            ->label('Not existing? Create one')
                            ->hidden(fn (Get $get) => !$get('grade_level_id'))
                            ->form([
                                Select::make('grade_level_id_new')
                                    ->required()
                                    ->relationship('gradeLevel', 'grade_name')
                                    ->default(fn (Get $get) => $get('grade_level_id')),

                                TextInput::make('section_name')
                                    ->required()
                                    ->unique(
                                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where(
                                            'grade_level_id',
                                            $get('grade_level_id_new')
                                        ),
                                    )
                            ])
                            ->action(function (array $data, Set $set): void {
                                $existing = Section::where('section_name', $data['section_name'])
                                    ->where('grade_level_id', $data['grade_level_id_new'])
                                    ->first();

                                // Use existing or create new
                                $section = $existing ?? Section::create([
                                    'section_name' => $data['section_name'],
                                    'grade_level_id' => $data['grade_level_id_new'],
                                ]);

                                if ($existing) {
                                    Notification::make()
                                        ->warning()
                                        ->duration(5000)
                                        ->title('Confirmation')
                                        ->body('Section (<b>'.$section->section_name.'</b>) name already exists for grade level <b>'.$section->gradeLevel->grade_name.'.')
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->success()
                                        ->duration(5000)
                                        ->title('Confirmation')
                                        ->body('New Section (<b>'.$section->section_name.'</b>) was successfully created for grade level <b>'.$section->gradeLevel->grade_name.'</b>.')
                                        ->send();
                                }

                                $set('section_id', $section->id);

                            })
                            ->modalWidth('md')
                            ->modalHeading('Create New Section')
                            ->modalSubmitActionLabel('Create')
                            ->modalFooterActionsAlignment('end')
                            ->closeModalByClickingAway(false)
                            ->modalCancelAction(false)
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title('Confirmation')
                                    ->body('Section successfully created.')
                            )
                    )
                    ->placeholder('Select options')
                    ->disabled(fn (Get $get) => !$get('grade_level_id'))
                    ->options(function (Get $get) {
                        $gradeId = $get('grade_level_id');
                        if (!$gradeId) {
                            return [];
                        }

                        return Section::where('grade_level_id', $gradeId)
                            ->notAssignedToClassroom()
                            ->orderBy('section_name')
                            ->pluck('section_name', 'id')
                            ->toArray();
                    })
                    ->required(),


                // Room Name Display
                TextInput::make('room_name')
                    ->label('Room Name')
                    ->required()
                    ->readOnly(),

                // Room Number
                TextInput::make('room_number')
                    ->default(fn () => Classroom::count() + 1),

                // Faculty Adviser
                Select::make('faculty_staff_id')
                    ->relationship('adviser', 'full_name', modifyQueryUsing: fn (
                        Builder $query
                    ) => $query->orderBy('last_name'))
                    ->label('Adviser')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->preload()
                    ->required()
                    ->searchable(),

                // Average Grade From
                TextInput::make('average_grade_from')
                    ->label('Average Grade From')
                    ->required()
                    ->numeric(2),

                // Average Grade To
                TextInput::make('average_grade_to')
                    ->label('Average Grade To')
                    ->required()
                    ->numeric(2),

                // Criteria Description
                TextInput::make('criteria_description')
                    ->label('Criteria Description')
                    ->maxLength(255),

                Select::make('school_year_from')
                    ->label('School Year From')
                    ->default(Carbon::now()->year)
                    ->options(
                        collect(range(Carbon::now()->year, Carbon::now()->year - 50))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    )
                    ->preload()
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set(
                        'school_year_to',
                        (int) $get('school_year_from') + 1
                    ))
                    ->required(),

                TextInput::make('school_year_to')
                    ->label('School Year To')
                    ->readOnly()
                    ->default(fn (Get $get) => (int) $get('school_year_from') + 1)
                    ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->queryStringIdentifier('classrooms')
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('room_name')
                    ->formatStateUsing(fn ($record): string => ucwords($record->room_name))
                    ->searchable(),

                Tables\Columns\TextColumn::make('gradeLevel.grade_name')
                    ->label('Grade Level')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('section.section_name')
                    ->label('Section')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('average_range')
                    ->label('Avg. Range')
                    ->getStateUsing(fn ($record): string => $record->average_grade_from.' - '.$record->average_grade_to)
                    ->searchable(),

                Tables\Columns\TextColumn::make('adviser.full_name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('school_year')
                    ->label('School Year')
                    ->getStateUsing(fn ($record) => $record->school_year_from.' - '.$record->school_year_to)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}
