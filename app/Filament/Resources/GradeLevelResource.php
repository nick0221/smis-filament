<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\GradeLevel;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GradeLevelResource\Pages;
use App\Filament\Resources\GradeLevelResource\RelationManagers;

class GradeLevelResource extends Resource
{
    protected static ?string $model = GradeLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grade_name')
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->queryStringIdentifier('grade_levels')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('grade_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sections_count')
                    ->label('Sections')
                    ->counts('sections')
                    ->sortable(),

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
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->size('sm')
                    ->label('Edit')
                    ->form([
                        Forms\Components\TextInput::make('grade_name')
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ])
                    ->modalHeading('Edit Grade Level')
                    ->modalFooterActionsAlignment('end')
                    ->closeModalByClickingAway(false)
                    ->modalCancelAction(false)
                    ->modalWidth('md')
                    ->successNotification(
                        Notification::make()
                        ->success()
                        ->title('Confirmation')
                        ->body('Grade level successfully updated.')
                    ),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
             GradeLevelResource\RelationManagers\SectionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeLevels::route('/'),
            'create' => Pages\CreateGradeLevel::route('/create'),
            'edit' => Pages\EditGradeLevel::route('/{record}/edit'),
        ];
    }
}
