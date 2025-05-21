<?php

namespace App\Filament\Resources;

use Dom\Text;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FacultyStaff;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FacultyStaffResource\Pages;
use App\Filament\Resources\FacultyStaffResource\RelationManagers;

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
                        ->maxDate(now())
                        ->label('Date of Birth'),
                    Forms\Components\Select::make('gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',

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


                    Forms\Components\Select::make('department_id')
                        ->relationship('department', 'name', fn ($query) => $query->orderBy('name', 'asc'))
                        ->searchable()
                        ->preload()
                        ->label('Department')
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->placeholder('Enter Department Name')
                                ->required(),
                            Forms\Components\TextInput::make('code')
                                ->placeholder('Enter Department Code'),
                        ])
                        ->editOptionForm([
                            Forms\Components\TextInput::make('name')

                                ->required(),

                            Forms\Components\TextInput::make('code'),
                        ])
                        ->createOptionAction(
                            fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                ->label('Create Department')
                                ->modalSubmitActionLabel('Save')
                                ->modalHeading('Add New Department')
                                ->modalWidth('md')
                                ->modalFooterActionsAlignment('end')
                        )
                        ->editOptionAction(
                            fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                ->label('Edit Department')
                                ->modalSubmitActionLabel('Save Changes')
                                ->modalHeading('Edit Department')
                                ->modalWidth('md')
                                ->modalFooterActionsAlignment('end')
                        ),



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
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->queryStringIdentifier('faculty_staff')
            ->recordUrl(fn (Model $record): string => route('filament.app.resources.faculty-staffs.view', ['record' => $record]))
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->defaultImageUrl(fn ($record): string => $record->profile_photo_url)
                    ->label('IMG')
                    ->alignCenter()
                    ->circular(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->description(fn ($record): string => $record->email)
                    ->getStateUsing(fn ($record): string => $record->full_name)
                    ->sortable(['last_name'])
                    ->searchable(['first_name', 'middle_name', 'last_name']),


                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation.title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department.name')
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
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('photo_path')
                            ->label('Profile Photo')
                            ->circular()
                            ->height('100px')
                            ->hiddenLabel()
                            ->columnSpan(1)
                            ->defaultImageUrl(fn ($record): string => $record->profile_photo_url),

                        TextEntry::make('full_name')
                            ->label('Name')
                            ->formatStateUsing(fn ($record): string => $record->full_name)
                            ->columnSpan(1),

                        TextEntry::make('gender')
                            ->label('Gender')
                            ->formatStateUsing(fn ($record): string => ucfirst($record->gender)),

                        TextEntry::make('dob')
                            ->label('Date of Birth')
                            ->date(),

                        TextEntry::make('age')
                            ->label('Age')
                            ->formatStateUsing(fn ($record): string => $record->age ? $record->age . ' years old' : 'N/A'),

                        TextEntry::make('address')
                            ->label('Address'),


                    ]),
                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('phone'),

                        TextEntry::make('email')
                            ->label('Email'),

                    ]),

                Section::make('Professional Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('designation.title')
                            ->label('Designation'),

                        TextEntry::make('department.name')
                            ->label('Department'),


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
            'index' => Pages\ListFacultyStaff::route('/'),
            'create' => Pages\CreateFacultyStaff::route('/create'),
            'edit' => Pages\EditFacultyStaff::route('/{record}/edit'),
            'view' => Pages\ViewFacultyStaff::route('/{record}'),
        ];
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return "{$record->full_name}";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Email' => $record->email,
            'Phone' => $record->phone,

        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return FacultyStaffResource::getUrl('view', ['record' => $record]);
    }



    public static function getNavigationBadge(): ?string
    {
        $facultyStaffCount = static::getModel()::count();
        if ($facultyStaffCount > 0) {
            return $facultyStaffCount;
        }

        return null;
    }

}
