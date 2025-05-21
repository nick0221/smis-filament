<?php

namespace App\Filament\Resources;

use App\Enums\StudentStatus;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EnrollmentResource\Pages;
use App\Filament\Resources\EnrollmentResource\RelationManagers;
use Filament\Forms\Get;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        $currentYear = Carbon::now()->year;
        $yearOptions = collect(range($currentYear, $currentYear - 30))->mapWithKeys(fn ($year) => [$year => $year])->toArray();


        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'full_name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('last_name', 'asc'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('initial_average_grade')
                    ->label('Average Grade')
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $state ? $set('section_id', null) : null),

                Forms\Components\Select::make('grade_level_id')
                    ->relationship('gradeLevel', 'grade_name')
                    ->label('Grade level')
                    ->live()
                    ->required(),

                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'section_name', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('grade_level_id', $get('grade_level_id'))->orderBy('section_name', 'asc'))
                    ->label('Section')
                    ->disabled(fn (Get $get) => $get('initial_average_grade'))
                    ->required(fn (Get $get) => !$get('initial_average_grade')),


                Forms\Components\Select::make('school_year_from')
                    ->preload()
                    ->searchable()
                    ->label('From')
                    ->default($currentYear)
                    ->options($yearOptions)
                    ->live()
                    ->required()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('school_year_to', $state + 1)),

                Forms\Components\TextInput::make('school_year_to')
                    ->label('To')
                    ->default($currentYear + 1)
                    ->readOnly()
                    ->required(),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->queryStringIdentifier('enrollments')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('student.profile_photo_url')
                    ->circular()
                    ->alignCenter()
                    ->label('IMG'),

                Tables\Columns\TextColumn::make('student_name')
                    ->getStateUsing(fn ($record): string => $record->student->full_name)
                    ->description(fn ($record): string => $record->classroom->room_name)
                    ->sortable(),

                Tables\Columns\TextColumn::make('classroom.adviser.full_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('school_year')
                    ->getStateUsing(fn ($record): string => $record->school_year_from.' - '.$record->school_year_to)
                    ->sortable(),

                Tables\Columns\TextColumn::make('initial_average_grade')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record): string => StudentStatus::tryFrom($record->status)->getColor() ?? 'secondary')
                    ->getStateUsing(fn ($record): string => StudentStatus::tryFrom($record->status)->name ?? 'Unknown')
                    ->searchable(),

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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
