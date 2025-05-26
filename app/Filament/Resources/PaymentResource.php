<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\SchoolExpense;
use App\Models\StudentPayment;
use Dom\Text;
use Fauzie811\FilamentListEntry\Infolists\Components\ListEntry;
use Filament\Facades\Filament;
use Filament\Forms\Components\{Fieldset, Group, Hidden, Placeholder, Select, Textarea, TextInput};
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;

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
                                    ->live()
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
                                    ->required(),

                                TextInput::make('gcash_pay_amount')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'gcash')
                                    ->default(fn (Get $get) => $get('amount'))
                                    ->label('GCash Pay Amount'),

                                TextInput::make('gcash_reference_number')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'gcash')
                                    ->label('GCash Reference Number'),

                                TextInput::make('bank_transfer_reference_number')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'bank_transfer')
                                    ->label('Bank Transfer Reference Number'),

                                TextInput::make('bank_name')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'bank_transfer')
                                    ->label('Bank Name'),

                                TextInput::make('bank_account_number')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'bank_transfer')
                                    ->label('Bank Account Number'),

                                TextInput::make('other_reference_number')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'other')
                                    ->label('Reference Number'),

                                Textarea::make('other_notes')
                                    ->columnSpanFull()
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'other')
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
                                    ->required(),


                                TextInput::make('change')
                                    ->label('Change')
                                    ->hidden(fn (Get $get) => $get('payment_method') !== 'cash')
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
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Model $record): string => route(
                'filament.app.resources.payments.view',
                ['record' => $record]
            ))
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->datetime('M d, Y h:i A')
                    ->label('Date'),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Payment Ref #'),

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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => 'Void this payment ' . $record->reference_number . '?')
                    ->icon('heroicon-o-trash')
                    ->label('Void')
                    ->successNotificationTitle(fn ($record) =>  $record->reference_number . ' has been voided.')
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
            ->columns(5)
            ->schema([
                \Filament\Infolists\Components\Group::make()
                    ->hiddenLabel()
                    ->columnSpan(3)
                    ->schema([
                         \Filament\Infolists\Components\Section::make('Student Details')
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

                        \Filament\Infolists\Components\Section::make('Payment Details')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label('Mode of Payment')
                                    ->formatStateUsing(fn ($record): string => ucwords($record->payment_method)),

                                TextEntry::make('pay_amount')
                                    ->prefix('₱')
                                    ->numeric(2)
                                    ->label('Amount'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn ($record): string => ucwords($record->status))
                                    ->color(fn ($record): string => $record->status === 'paid' ? 'success' : 'danger'),

                                TextEntry::make('payment_date')
                                    ->label('Payment Date')
                                    ->datetime('M d, Y h:i A'),

                            ]),

                    ]),

                \Filament\Infolists\Components\Group::make()
                    ->columnSpan(2)
                    ->schema([
                        \Filament\Infolists\Components\Fieldset::make('total_fees')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('schoolExpense.fees.fee_amount')
                                    ->inlineLabel()
                                    ->alignEnd()
                                    ->columnSpanFull()
                                    ->label('Total Fees')
                                    ->extraAttributes(['class' => 'font-bold'])
                                    ->formatStateUsing(fn ($record): string =>  number_format($record->schoolExpense->fees->sum('fee_amount'), 2)),


                            ]),

                        ListEntry::make('schoolExpense')
                            ->label('Tuition and Miscellaneous Fees')
                            ->itemIcon('heroicon-o-arrow-long-right')
                            ->itemIconColor('primary')
                            ->getStateUsing(
                                fn ($record) =>
                                        $record->schoolExpense->fees->map(fn ($fee) => [
                                            'label' => $fee->fee_name,
                                            'description' => $fee->fee_amount ?? '',
                                        ])->toArray()
                            )
                            ->itemLabel(fn ($record) => $record['label'])
                            ->itemDescription(fn ($record) => 'Fee: ₱ '.$record['description']),


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
