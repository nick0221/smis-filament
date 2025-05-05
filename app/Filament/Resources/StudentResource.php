<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Student Form')
                ->tabs([
                    Tab::make('Personal Info')
                        ->schema([
                            \Filament\Forms\Components\Grid::make()
                                ->schema([
                                    \Filament\Forms\Components\TextInput::make('first_name')
                                        ->label('First Name')
                                        ->autofocus()
                                        ->placeholder('Enter first name')
                                        ->required(),

                                    \Filament\Forms\Components\TextInput::make('middle_name')
                                        ->placeholder('Enter middle name')
                                        ->label('Middle Name'),

                                    \Filament\Forms\Components\TextInput::make('last_name')
                                        ->label('Last Name')
                                        ->placeholder('Enter last name')
                                        ->required(),

                                    \Filament\Forms\Components\TextInput::make('extension_name')
                                        ->hint('Optional')
                                        ->hintColor('muted')
                                        ->placeholder('Enter extension name (e.g. Jr., Sr., III)')
                                        ->label('Extension Name'),

                                    \Filament\Forms\Components\DatePicker::make('dob')
                                        ->label('Date of Birth')
                                        ->required()
                                        ->placeholder('Select date of birth')
                                        ->format('M d, Y')
                                        ->closeOnDateSelection()
                                        ->maxDate(now())
                                        ->native(false),

                                    \Filament\Forms\Components\Select::make('gender')
                                        ->options([
                                            'male' => 'Male',
                                            'female' => 'Female',
                                        ])
                                        ->required(),
                                        \Filament\Forms\Components\FileUpload::make('image')
                                        ->label('Student Image')
                                        ->image()
                                        ->directory('students')
                                        ->imageCropAspectRatio('1:1')
                                        ->imageResizeTargetWidth(300)
                                        ->imageResizeTargetHeight(300),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'sm' => 2,

                                ]),
                        ]),

                    Tab::make('Academic Info')
                        ->schema([
                            \Filament\Forms\Components\Grid::make()
                                ->schema([
                                    \Filament\Forms\Components\Select::make('teacher_id')
                                        ->relationship('teacher', 'first_name')
                                        ->searchable(),

                                    \Filament\Forms\Components\Select::make('class_room_id')
                                        ->relationship('classRoom', 'name')
                                        ->searchable(),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'sm' => 2,
                                ]),
                        ]),

                    Tab::make('Contact Info')
                        ->schema([
                            \Filament\Forms\Components\Grid::make()
                                ->schema([
                                    \Filament\Forms\Components\TextInput::make('email')->email()->unique(ignoreRecord: true),
                                    \Filament\Forms\Components\TextInput::make('phone'),
                                    \Filament\Forms\Components\TextInput::make('address'),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'sm' => 2,
                                ]),
                        ]),

                    Tab::make('Family Details')
                        ->schema([
                            \Filament\Forms\Components\Grid::make()->schema([
                                \Filament\Forms\Components\Repeater::make('familyMembers')
                                    ->relationship() // Filament handles hasMany automatically
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('name')->required()->live(onBlur: true),
                                        \Filament\Forms\Components\Select::make('relationship')->options([
                                            'father' => 'Father',
                                            'mother' => 'Mother',
                                            'guardian' => 'Guardian',
                                        ])->required()->live(onBlur: true),
                                        \Filament\Forms\Components\TextInput::make('contact')->required(),
                                        \Filament\Forms\Components\TextInput::make('address'),
                                        \Filament\Forms\Components\TextInput::make('occupation'),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => strtoupper($state['name']) .' - '. strtoupper($state['relationship']) ?? null)
                                    ->grid(['default' => 1, 'sm' => 2])
                                    ->label('Family Details')
                                    ->defaultItems(1)
                                    ->maxItems(3)
                                    ->minItems(1)
                                    ->addActionLabel('Add Family Member')
                                    ->cloneable()
                                    ->columnSpanFull()
                                    ->addActionAlignment('right'),
                            ])->columns(['default' => 1, 'sm' => 2]),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(fn ($record) => $record->profile_photo_url)
                    ->circular()
                    ->alignCenter()
                    ->label('IMG'),

                Tables\Columns\TextColumn::make('full_name')
                    ->wrap()
                    ->label('Full Name')

                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(),

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
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
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

            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading();
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
        ];
    }
}
