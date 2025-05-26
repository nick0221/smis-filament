<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\SchoolExpense;
use App\Models\StudentPayment;
use Filament\Forms\Components\{Fieldset, Group, Hidden, Placeholder, Section, Select, TextInput};
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class PaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Accounting & Finance';

    protected static ?string $navigationLabel = 'Payment Records';


    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Group::make()
                    ->columnSpan(3)
                    ->hiddenLabel()
                    ->schema([
                        Fieldset::make('enrollment_reference_number')
                            ->label('Enrollment Reference Number')
                            ->schema([
                                Select::make('enrollment_id')
                                    ->hiddenLabel()
                                    ->columnSpan(2)
                                    ->placeholder('Enter Reference Number')
                                    ->relationship(
                                        name: 'enrollment',
                                        titleAttribute: 'reference_number',
                                        modifyQueryUsing: fn (Builder $query) => $query->with('student')
                                    )
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Enrollment::query()
                                            ->where('reference_number', 'like', "%{$search}%")
                                            ->orWhereHas(
                                                'student',
                                                fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                                                    ->orWhere('last_name', 'like', "%{$search}%")
                                            )
                                            ->limit(10)
                                            ->get()
                                            ->pluck('reference_number', 'id');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $enrollment = Enrollment::with('student')->find($state);
                                        if ($enrollment && $enrollment->student) {
                                            $student = $enrollment->student;
                                            $set('student_first_name', $student->first_name);
                                            $set('student_last_name', $student->last_name);

                                        } else {
                                            $set('student_first_name', null);
                                            $set('student_last_name', null);

                                        }
                                    })
                                    ->required(),


                            ])->columnSpanFull(),


                        Fieldset::make('Student Information')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([

                                TextInput::make('student_first_name')
                                    ->columnSpan(1)
                                    ->readOnly()
                                    ->label('First Name'),

                                TextInput::make('student_last_name')
                                    ->columnSpan(1)
                                    ->readOnly()
                                    ->label('Last Name'),


                            ]),

                        Fieldset::make('Payment Information')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->default(function () {
                                        $schoolExpense = SchoolExpense::where('is_active', true)->latest()->first();
                                        return $schoolExpense?->fees->sum('fee_amount') ?? 0;
                                    })
                                    ->label('Amount Due'),

                                Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->default('cash')
                                    ->options([
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer',
                                        'gcash' => 'GCash',
                                        'other' => 'Other',
                                    ])
                                    ->required(),

                                TextInput::make('cash_tendered')
                                    ->label('Cash Tendered')
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->prefix('₱')
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $cash = (float) $state;
                                        $amount = (float) $get('amount');

                                        $change = max($cash - $amount, 0);
                                        $set('change', $change);
                                    })
                                    ->required(),


                                TextInput::make('change')
                                    ->label('Change')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('₱')
                                    ->required(),


                            ]),
                    ]),

                Group::make()
                    ->columnSpan(2)
                    ->hiddenLabel()
                    ->schema([
                        Fieldset::make('Tuition & Miscellaneous Fees')
                            ->columnSpanFull()
                            ->schema([
                                Hidden::make('school_expense_id')
                                    ->default(function () {
                                        $schoolExpense = SchoolExpense::where('is_active', true)->latest()->first();
                                        return $schoolExpense?->id;
                                    }),
                                Placeholder::make('active_tuitions')
                                    ->hiddenLabel()
                                    ->content(fn () => new HtmlString(
                                        SchoolExpense::renderActiveTuitionTable()
                                    ))
                                    ->columnSpanFull()
                            ])
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Payment Ref #') ,

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student')
                    ->searchable([
                        'enrollment.student.first_name',
                        'enrollment.student.last_name',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(fn ($record): string => ucwords($record->payment_method))
                    ->searchable(),

                Tables\Columns\TextColumn::make('pay_amount')
                    ->label('Payment Amount'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($record): string => ucwords($record->status))
                    ->color(fn ($record): string => $record->status === 'paid' ? 'success' : 'danger')
                    ->searchable(),





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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
