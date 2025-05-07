<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacultyStaffResource\Pages;
use App\Filament\Resources\FacultyStaffResource\RelationManagers;
use App\Models\FacultyStaff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacultyStaffResource extends Resource
{
    protected static ?string $model = FacultyStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Personal Information')
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->label('First Name'),
                    Forms\Components\TextInput::make('middle_name')
                        ->label('Middle Name'),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->label('Last Name'),
                    Forms\Components\TextInput::make('extension_name')
                        ->label('Extension (e.g. Jr., Sr.)'),
                    Forms\Components\DatePicker::make('dob')
                        ->label('Date of Birth'),
                    Forms\Components\Select::make('gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'other' => 'Other',
                        ])
                        ->required(),
                ]),

            Forms\Components\Section::make('Contact Information')
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->label('Phone Number'),
                    Forms\Components\TextInput::make('address')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Professional Details')
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->schema([
                    Forms\Components\Select::make('designation_id')
                        ->label('Designation')
                        ->relationship('designation', 'title')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('title')
                                ->placeholder('Enter Designation Title')
                                ->required(),
                        ])
                        ->editOptionForm([
                            Forms\Components\TextInput::make('title')
                            ->required(),
                        ])
                        ->editOptionAction(
                            fn (\Filament\Forms\Components\Actions\Action $action) => $action
                            ->label('Edit Designation')
                            ->modalSubmitActionLabel('Save Changes')
                            ->modalHeading('Edit Designation')
                            ->modalWidth('sm')
                            ->modalFooterActionsAlignment('end')
                        )
                        ->createOptionAction(
                            fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                ->label('Create Designation')
                                ->modalSubmitActionLabel('Save')
                                ->modalHeading('Add New Designation')
                                ->modalWidth('sm')
                                ->modalFooterActionsAlignment('end')

                         ) ,


                    Forms\Components\TextInput::make('department')
                        ->label('Department'),
                    Forms\Components\FileUpload::make('photo_path')
                        ->label('Photo')
                        ->image()
                        ->directory('faculty-photos'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('extension_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo_path')
                    ->searchable(),
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
            'index' => Pages\ListFacultyStaff::route('/'),
            'create' => Pages\CreateFacultyStaff::route('/create'),
            'edit' => Pages\EditFacultyStaff::route('/{record}/edit'),
        ];
    }
}
