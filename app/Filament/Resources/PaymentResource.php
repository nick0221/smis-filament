<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\SchoolExpense;
use App\Models\StudentPayment;
use Dom\Text;
use Filament\Forms\Components\{Fieldset, Group, Hidden, Placeholder, Select, Textarea, TextInput};
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Accounting & Finance';

    protected static ?string $navigationLabel = 'Payment Records';


    public static function form(Form $form): Form
    {
        $hiddenUnless = fn (string $method) => fn (Get $get) => $get('payment_method') !== $method;
        $requiredIf = fn (string $method) => fn (Get $get) => $get('payment_method') === $method;


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
                                    ->label('Enrollment')
                                    ->columnSpan(2)
                                    ->placeholder('Enter Reference Number')
                                    ->preload()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Enrollment::query()
                                            ->with('student') // Eager load
                                            ->where('reference_number', 'like', "%{$search}%")
                                            ->orWhereHas(
                                                'student',
                                                fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                                                    ->orWhere('last_name', 'like', "%{$search}%")
                                            )
                                            ->limit(10)
                                            ->get()
                                            ->mapWithKeys(function ($enrollment) {
                                                $student = $enrollment->student;
                                                return [$enrollment->id => "{$enrollment->reference_number} - {$student->first_name} {$student->last_name}"];
                                            });
                                    })
                                    ->searchable()
                                    ->required()

                            ])->columnSpanFull(),


                        Fieldset::make('Payment Information')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->default(function () {
                                        $schoolExpense = SchoolExpense::where('is_active', true)->latest()->first();
                                        return $schoolExpense?->fees->sum('fee_amount') ?? 0;
                                    })
                                    ->readOnly()
                                    ->label('Amount Due'),

                                Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->default('cash')
                                    ->live()
                                    ->options([
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer',
                                        'gcash' => 'GCash',
                                        'other' => 'Other',
                                    ])
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $amount = $get('amount');
                                        $payFields = [
                                            'gcash' => 'gcash_pay_amount',
                                            'bank_transfer' => 'bank_pay_amount',
                                            'other' => 'other_pay_amount',
                                            'cash' => 'pay_amount',
                                        ];

                                        foreach ($payFields as $method => $field) {
                                            $set($field, $state === $method ? $amount : null);
                                        }


                                    })
                                    ->required(),

                                TextInput::make('gcash_pay_amount')
                                    ->hidden($hiddenUnless('gcash'))
                                    ->required($requiredIf('gcash'))
                                    ->label('GCash Pay Amount'),

                                TextInput::make('gcash_reference_number')
                                    ->hidden($hiddenUnless('gcash'))
                                    ->required($requiredIf('gcash'))
                                    ->label('GCash Reference Number'),

                                TextInput::make('bank_pay_amount')
                                    ->hidden($hiddenUnless('bank_transfer'))
                                    ->required($requiredIf('bank_transfer'))
                                    ->label('Bank Pay Amount'),

                                TextInput::make('bank_reference_number')
                                    ->hidden($hiddenUnless('bank_transfer'))
                                    ->required($requiredIf('bank_transfer'))
                                    ->label('Bank Transfer Reference Number'),

                                TextInput::make('bank_name')
                                    ->hidden($hiddenUnless('bank_transfer'))
                                    ->required($requiredIf('bank_transfer'))
                                    ->label('Bank Name'),

                                TextInput::make('bank_account_number')
                                    ->hidden($hiddenUnless('bank_transfer'))
                                    ->required($requiredIf('bank_transfer'))
                                    ->label('Bank Account Number'),

                                TextInput::make('other_pay_amount')
                                    ->hidden($hiddenUnless('other'))
                                    ->required($requiredIf('other'))
                                    ->label('Paid Amount'),

                                TextInput::make('other_reference_number')
                                    ->hidden($hiddenUnless('other'))
                                    ->required($requiredIf('other'))
                                    ->label('Reference Number'),

                                Textarea::make('other_notes')
                                    ->columnSpanFull()
                                    ->hidden($hiddenUnless('other'))
                                    ->label('Notes'),


                                TextInput::make('cash_tendered')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'cash')
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
                                    ->required(fn (Get $get) => $get('payment_method') === 'cash'),


                                TextInput::make('change')
                                    ->label('Change')
                                    ->hidden($hiddenUnless('cash'))
                                    ->required($requiredIf('cash'))
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('₱')


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
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->recordUrl(false)
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->datetime('M d, Y h:i A')
                    ->label('Date'),

                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->label('Payment Ref #'),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student')
                    ->searchable([
                        'first_name',
                        'last_name',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(fn ($record): string => ucwords($record->payment_method)),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->getStateUsing(fn ($record) => match ($record->payment_method) {
                        'cash'  => number_format($record->pay_amount, 2),
                        'gcash' => number_format($record->gcash_pay_amount, 2),
                        'bank_transfer' => number_format($record->bank_pay_amount, 2),
                        'other' => number_format($record->other_pay_amount, 2),
                        default => '-',
                    })
                    ->label('Paid Amount'),


                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($record): string => ucwords($record->status))
                    ->color(fn ($record): string => $record->status === 'paid' ? 'success' : 'danger'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View more details')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->hiddenLabel()
                    ->link(),


                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => 'Void this payment '.$record->reference_number.'?')
                    ->icon('heroicon-o-trash')
                    ->label('Void')
                    ->hiddenLabel()
                    ->link()
                    ->successNotificationTitle(fn ($record) => $record->reference_number.' has been voided.')
                    ->after(function ($record) {
                        $record->status = 'void';
                        $record->save();
                    }),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(4)
            ->schema([
                \Filament\Infolists\Components\Group::make()
                    ->hiddenLabel()
                    ->columnSpan(3)
                    ->schema([
                        Section::make('Student Details')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('enrollment.reference_number')
                                    ->label('Enrollment Ref #'),

                                TextEntry::make('enrollment.student.full_name')
                                    ->label('Name')
                                    ->columnSpan(2)
                                    ->formatStateUsing(fn ($record): string => $record->enrollment->student->full_name),

                                TextEntry::make('enrollment.student.student_id_number')
                                    ->columnSpanFull()
                                    ->label('Student ID'),

                            ]),

                        Section::make('Payment Details')
                            ->columns(3)
                            ->schema([

                                TextEntry::make('payment_date')
                                    ->label('Payment Date')
                                    ->datetime('M d, Y h:i A'),

                                TextEntry::make('payment_method')
                                    ->label('Mode of Payment')
                                    ->formatStateUsing(fn ($record): string => ucwords($record->payment_method)),

                                TextEntry::make('pay_amount')
                                    ->numeric(2)
                                    ->visible(fn ($record): bool => $record->payment_method === 'cash')
                                    ->label('Cash Amount'),

                                TextEntry::make('bank_pay_amount')
                                    ->numeric(2)
                                    ->visible(fn ($record): bool => $record->payment_method === 'bank_transfer')
                                    ->label('Paid Amount'),

                                TextEntry::make('other_pay_amount')
                                    ->numeric(2)
                                    ->visible(fn ($record): bool => $record->payment_method === 'other')
                                    ->label('Paid Amount'),


                                TextEntry::make('gcash_pay_amount')
                                    ->numeric(2)
                                    ->visible(fn ($record): bool => $record->payment_method === 'gcash')
                                    ->label('Gcash Amount'),
                                TextEntry::make('gcash_reference_number')
                                    ->visible(fn ($record): bool => $record->payment_method === 'gcash')
                                    ->label('Gcash Ref #'),


                                TextEntry::make('bank_reference_number')
                                    ->visible(fn ($record): bool => $record->payment_method === 'bank_transfer')
                                    ->label('Receipt/Bank Ref #'),
                                TextEntry::make('bank_name')
                                    ->visible(fn ($record): bool => $record->payment_method === 'bank_transfer')
                                    ->label('Bank Name'),
                                TextEntry::make('bank_account_number')
                                    ->visible(fn ($record): bool => $record->payment_method === 'bank_transfer')
                                    ->columnSpan(2)
                                    ->label('Bank Account #'),

                                TextEntry::make('other_reference_number')
                                    ->visible(fn ($record): bool => $record->payment_method === 'other')
                                    ->label('Ref #'),

                                TextEntry::make('other_notes')
                                    ->visible(fn ($record): bool => $record->payment_method === 'other')
                                    ->label('Notes'),

                            ]),

                    ]),

                \Filament\Infolists\Components\Group::make()
                    ->schema([
                        \Filament\Infolists\Components\Fieldset::make('total_fees')
                            ->columnSpanFull()
                            ->columns(1)
                            ->hiddenLabel()
                            ->schema([

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn ($record): string => ucwords($record->status))
                                    ->color(fn ($record): string => $record->status === 'paid' ? 'success' : 'danger'),

                                TextEntry::make('created_at')
                                    ->label('Transaction Date')
                                    ->datetime('M d, Y h:i A'),

                            ]),


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
            'view' => Pages\ViewStudentPayment::route('/{record}'),
        ];
    }


}
