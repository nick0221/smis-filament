<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\ClassRoom;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClassRoomResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClassRoomResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\FormsComponent;
use Illuminate\Database\Eloquent\Model;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {


        return $form
            ->schema([
                Forms\Components\Select::make('grade_level_id')
                        ->label('Grade Level')
                        ->relationship(
                            'gradeLevel',
                            'grade_name',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('grade_name')
                        )
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $grade = $get('grade_level_id');
                            $section = $get('section_id');
                            if ($grade && $section) {
                                $gradeName = \App\Models\GradeLevel::find($grade)?->grade_name;
                                $sectionName = \App\Models\Section::find($section)?->section_name;
                                $set('room_name', "{$gradeName} - {$sectionName}");
                            }
                        }),

                Forms\Components\Select::make('section_id')
                        ->label('Section')
                        ->relationship(
                            'section',
                            'section_name',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('section_name')
                        )
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $grade = $get('grade_level_id');
                            $section = $get('section_id');
                            if ($grade && $section) {
                                $gradeName = \App\Models\GradeLevel::find($grade)?->grade_name;
                                $sectionName = \App\Models\Section::find($section)?->section_name;
                                $set('room_name', "{$gradeName} - {$sectionName}");
                            }
                        }),

                Forms\Components\TextInput::make('room_name')
                        ->label('Room Name')
                        ->required()
                        ->readOnly(), // prevent manual input

                Forms\Components\TextInput::make('room_number')
                        ->default(fn () => Classroom::count() + 1),

                Forms\Components\Select::make('faculty_staff_id')
                        ->relationship('adviser', 'full_name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('last_name', 'asc'))
                        ->label('Adviser')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                        ->searchable(['first_name', 'middle_name', 'last_name'])
                        ->preload()
                        ->searchable(),

                Forms\Components\TextInput::make('average_grade_from')
                        ->label('Average Grade From')
                        ->numeric(2),

                Forms\Components\TextInput::make('average_grade_to')
                        ->label('Average Grade To')
                        ->numeric(2),

                Forms\Components\TextInput::make('criteria_description')



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
                Tables\Columns\TextColumn::make('section.section_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gradeLevel.id')
                    ->numeric()
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
