<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\ClassRoom;
use App\Models\GradeLevel;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\FormsComponent;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClassRoomResource\Pages;
use App\Filament\Resources\ClassRoomResource\RelationManagers;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {


        return $form
            ->schema([

                Select::make('grade_level_id')

                    ->required()
                    ->label('Grade Level')
                    ->relationship(
                        'gradeLevel',
                        'grade_name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('grade_name')
                    )
                    ->live()
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

                Select::make('section_id')
                    ->required()
                    ->label('Section')
                    ->options(function (Get $get) {
                        $gradeId = $get('grade_level_id');
                        if (!$gradeId) {
                            return [];
                        }

                        return Section::where('grade_level_id', $gradeId)
                            ->orderBy('section_name')
                            ->pluck('section_name', 'id')
                            ->toArray();
                    })
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $grade = $get('grade_level_id');
                        $section = $get('section_id');
                        if ($grade && $section) {
                            $gradeName = GradeLevel::find($grade)?->grade_name;
                            $sectionName = Section::find($section)?->section_name;
                            $set('room_name', "{$gradeName} - {$sectionName}");
                        }
                    }),

                TextInput::make('room_name')
                    ->label('Room Name')
                    ->required()
                    ->readOnly(),

                TextInput::make('room_number')
                    ->default(fn () => Classroom::count() + 1),

                Select::make('faculty_staff_id')
                    ->relationship('adviser', 'full_name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('last_name'))
                    ->label('Adviser')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->preload()
                    ->required()
                    ->searchable(),

                TextInput::make('average_grade_from')
                    ->label('Average Grade From')
                    ->required()
                    ->numeric(2),

                TextInput::make('average_grade_to')
                    ->label('Average Grade To')
                    ->required()
                    ->numeric(2),

                TextInput::make('criteria_description')
                    ->label('Criteria Description')
                    ->maxLength(255),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('gradeLevel.id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('section.section_name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('adviser.full_name')
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
