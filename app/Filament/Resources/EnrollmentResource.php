<?php

namespace App\Filament\Resources;

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
use Dom\Text;

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

                Forms\Components\Select::make('grade_level_id')
                    ->preload()
                    ->searchable()
                    ->relationship('gradeLevel', 'grade_name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('grade_name', 'asc'))
                    ->required(),

                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'section_name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('section_name', 'asc')),

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
            ->columns([
                Tables\Columns\TextColumn::make('student.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gradeLevel.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
