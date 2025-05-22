<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Group;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\StudentResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use Filament\Forms\Components\{Tabs,  Grid, TextInput, DatePicker, Select, FileUpload, Repeater, Textarea};

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Student Form')
                ->tabs([
                    Tab::make('Personal Info')
                        ->icon('heroicon-m-identification')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    TextInput::make('first_name')->label('First Name')->autofocus()->required(),
                                    TextInput::make('middle_name')->label('Middle Name'),
                                    TextInput::make('last_name')->label('Last Name')->required(),
                                    TextInput::make('extension_name')->label('Extension Name')->hint('Optional')->hintColor('muted'),
                                    DatePicker::make('dob')->label('Date of Birth')->required()->maxDate(now())->native(false)->closeOnDateSelection(),
                                    Select::make('gender')->options(['male' => 'Male', 'female' => 'Female'])->required(),
                                    FileUpload::make('image')->label('Student Image')->image()->directory('students')
                                        ->imageCropAspectRatio('1:1')->imageResizeTargetWidth(300)->imageResizeTargetHeight(300),
                                ])
                                ->columns(['default' => 1, 'sm' => 2]),
                        ]),

                    Tab::make('Contact Info')
                        ->icon('heroicon-m-phone')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    TextInput::make('email')->email()->unique(ignoreRecord: true),
                                    TextInput::make('phone'),
                                    TextInput::make('address'),
                                ])
                                ->columns(['default' => 1, 'sm' => 2]),
                        ]),

                    Tab::make('Family Details')
                        ->icon('heroicon-m-users')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Repeater::make('familyMembers')
                                        ->relationship()
                                        ->schema([
                                            TextInput::make('name')->required(),
                                            Select::make('relationship')
                                                ->options(['father' => 'Father', 'mother' => 'Mother', 'guardian' => 'Guardian'])
                                                ->required(),
                                            TextInput::make('contact')->required(),
                                            TextInput::make('address'),
                                            TextInput::make('occupation'),
                                        ])
                                        ->itemLabel(
                                            fn (array $state): ?string =>
                                            strtoupper($state['name'] ?? '') . ' - ' . strtoupper($state['relationship'] ?? '')
                                        )
                                        ->label('Family Details')
                                        ->defaultItems(1)
                                        ->maxItems(3)
                                        ->minItems(1)
                                        ->cloneable()
                                        ->grid(['default' => 1, 'sm' => 3])
                                        ->columnSpanFull()
                                        ->addActionLabel('Add Family Member'),
                                ])
                                ->columns(['default' => 1, 'sm' => 2]),
                        ]),

                    // NEW DOCUMENTS TAB
                    Tab::make('Documents')
                        ->icon('heroicon-m-paper-clip')
                        ->schema([
                            Repeater::make('documents')
                                ->relationship()
                                ->label('Document attachments')
                                ->defaultItems(1)
                                ->schema([
                                    TextInput::make('title')->required()->placeholder('e.g. Birth Certificate'),
                                    Textarea::make('description')->rows(2)->placeholder('Optional notes'),
                                    FileUpload::make('file_path')
                                        ->label('File')
                                        ->disk('public')
                                        ->directory('student-documents')
                                        ->required()
                                        ->previewable()
                                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                                ])
                                ->grid(['default' => 1, 'sm' => 3])
                                ->defaultItems(0)
                                ->addActionLabel('Add Document')
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->queryStringIdentifier('students')
            ->recordUrl(fn (Model $record): string => route('filament.app.resources.students.view', ['record' => $record]))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(fn ($record) => $record->profile_photo_url)
                    ->circular()
                    ->alignCenter()
                    ->label('IMG'),

                Tables\Columns\TextColumn::make('full_name')
                    ->wrap()
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->description(fn ($record) => $record->student_id_number)
                    ->sortable(['last_name']),

                Tables\Columns\TextColumn::make('dob')
                    ->label('Date of Birth')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(function ($record) {
                        return match ($record->gender) {
                            'male' => 'Male',
                            'female' => 'Female',
                            'other' => 'Other',
                        };
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->default('-'),

                Tables\Columns\TextColumn::make('phone')
                    ->default('-'),

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
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()->label('View more'),
                ])
            ])

            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            \Filament\Infolists\Components\Tabs::make('Student Details')
                ->columnSpanFull()
                ->tabs([
                    \Filament\Infolists\Components\Tabs\Tab::make('Profile')
                        ->columns(2)
                        ->schema([
                            ImageEntry::make('image')
                                ->label('Profile Photo')
                                ->circular()
                                ->height('100px')
                                ->hiddenLabel()
                                ->columnSpan(1)
                                ->defaultImageUrl(fn ($record) => $record->profile_photo_url),

                            TextEntry::make('full_name')
                                ->label('Name')
                                ->formatStateUsing(fn ($record) => $record->full_name)
                                ->columnSpan(1),


                            TextEntry::make('gender')
                                ->label('Gender')
                                ->formatStateUsing(fn ($record) => ucfirst($record->gender)),

                            TextEntry::make('dob')
                                ->label('Date of Birth')
                                ->date(),

                            TextEntry::make('email')
                                ->default('-')
                                ->label('Email'),

                            TextEntry::make('age')
                                ->label('Age')
                                ->formatStateUsing(fn ($record) => $record->age ? $record->age . ' years old' : 'N/A'),

                            TextEntry::make('phone')
                                ->default('-')
                                ->label('Phone'),

                            TextEntry::make('address')
                                ->default('-')
                                ->label('Address'),
                        ]),

                    \Filament\Infolists\Components\Tabs\Tab::make('Family Info')
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextEntry::make('no_family_members')
                                        ->hiddenLabel()
                                        ->visible(fn (Student $record): string => $record->familyMembers->isEmpty())
                                        ->state('No family member information available.')
                                        ->columnSpanFull()
                                        ->alignCenter()
                                        ->icon('heroicon-m-information-circle')
                                        ->extraAttributes(['class' => 'italic'])
                                        ->color('muted'),

                                    RepeatableEntry::make('familyMembers')
                                        ->hiddenLabel()
                                        ->visible(fn ($record) => !empty($record->familyMembers))
                                        ->grid(['default' => 1, 'sm' => 2])
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('name')->label('Name'),
                                            TextEntry::make('relationship')
                                                ->formatStateUsing(fn ($record) => ucfirst($record->relationship))
                                                ->label('Relationship'),
                                            TextEntry::make('contact')->label('Contact'),
                                            TextEntry::make('address')
                                                ->columnSpanFull()
                                                ->label('Address'),
                                        ]),
                                ]),
                        ]),

                    \Filament\Infolists\Components\Tabs\Tab::make('Academic Info')
                        ->schema([
                            Group::make()
                                ->schema([
                                    // Add academic info entries here if needed
                                ]),
                        ]),

                    \Filament\Infolists\Components\Tabs\Tab::make('Documents')
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextEntry::make('no_documents')
                                        ->hiddenLabel()
                                        ->visible(fn (Student $record): string => $record->documents->isEmpty())
                                        ->state('No document information available.')
                                        ->columnSpanFull()
                                        ->alignCenter()
                                        ->icon('heroicon-m-information-circle')
                                        ->extraAttributes(['class' => 'italic'])
                                        ->color('muted'),

                                    RepeatableEntry::make('documents')
                                        ->contained(false)
                                        ->hiddenLabel()
                                        ->visible(fn ($record) => !empty($record->documents))
                                        ->columns(3)
                                        ->schema([
                                            TextEntry::make('title')
                                                ->label('Document Name'),

                                            TextEntry::make('file_path')
                                                ->label('View / Download')
                                                ->url(fn ($record) => asset($record->file_path))
                                                ->openUrlInNewTab()
                                                ->state('View Document')
                                                ->color('primary')
                                                ->icon('heroicon-o-arrow-down-tray'),

                                            TextEntry::make('description')
                                                ->label('Description'),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);
    }



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('deleted_at');
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'student_id_number'
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return "{$record->full_name}";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'ID' => $record->student_id_number,
            'Email' => $record->email,
            'Phone' => $record->phone,

        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return StudentResource::getUrl('view', ['record' => $record]);
    }







    public static function getNavigationBadge(): ?string
    {
        $studentCount = static::getModel()::count();
        if ($studentCount > 0) {
            return $studentCount;
        }

        return null;

    }

}
