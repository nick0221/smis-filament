<?php

namespace App\Filament\Resources\EnrollmentResource\RelationManagers;

use App\Models\StudentDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StudentDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentDocuments';

    protected static ?string $title = 'Student Documents';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        $countDocuments = (int) $this->ownerRecord->studentDocuments()->count();


        return $table
            ->heading('Student Documents  '. ($countDocuments > 0 ? "({$countDocuments})" : ''))
            ->deferLoading()
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')
            ->recordAction(false)
            ->columns([
                Tables\Columns\IconColumn::make('file_path')
                    ->label('')
                    ->icon(fn ($record) => $record->file_path ? 'heroicon-o-arrow-down-tray' : null)
                    ->color('primary')
                    ->url(fn ($record) => $record->file_path ? asset("storage/{$record->file_path}") : null)
                    ->openUrlInNewTab()
                    ->size('sm')
                    ->tooltip(fn ($record) => $record->file_path ? 'View/Download '.Str::limit(
                        $record->title,
                        20,
                        '...'
                    ) : null)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('title')
                    ->getStateUsing(fn ($record): string => $record->title ?? 'N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date uploaded')
                    ->since()
                //->dateTime('M d, Y - h:i A'),


            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->link()
                    ->slideOver()
                    ->label('Upload Documents')
                    ->modalHeading('Upload Student Documents')
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->modalFooterActionsAlignment('end')
                    ->modalSubmitActionLabel('Submit')
                    ->form([
                        Forms\Components\Repeater::make('documents')
                            ->label('Documents')
                            ->hiddenLabel()
                            ->grid(1)
                            ->columnSpanFull()
                            ->columns(3)
                            ->schema([
                                Forms\Components\Textarea::make('title')
                                    ->label('Document name')
                                    ->live()
                                    ->required()
                                    ->maxLength(150),

                                Forms\Components\Textarea::make('description')
                                    ->maxLength(255),

                                Forms\Components\FileUpload::make('file_path')
                                    ->label('File')
                                    ->disk('public')
                                    ->directory('student-documents')
                                    ->required()
                                    ->previewable()
                                    ->acceptedFileTypes(['application/pdf', 'image/*']),
                            ])
                            ->itemLabel(
                                fn (array $state): ?string => strtoupper($state['title'] ?? '')
                            )
                            ->minItems(1)
                            ->defaultItems(1)
                            ->collapsible()
                            ->reorderableWithDragAndDrop(false)
                            ->addActionLabel('Add more document'),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        // Delegate to model method for clean code
                        $livewire->getRelationship()->getParent()->uploadDocuments($data['documents']);
                    })
                    ->createAnother(false)
                    ->successNotification(Notification::make()->success()->title('Documents uploaded successfully.'))

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip(fn ($record) => 'Upload Document for '.Str::limit($record->title, 20, '...'))
                    ->hiddenLabel()
                    ->color('primary')
                    ->size('lg')
                    ->closeModalByClickingAway(false)
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Edit Document')
                    ->modalWidth('sm')
                    ->modalAlignment('center')
                    ->modalFooterActionsAlignment('center')
                    ->form([
                        Forms\Components\FileUpload::make('file_path')
                            ->disk('public')
                            ->directory('student-documents')
                            ->label('File')
                            ->required()
                            ->previewable()
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ])
                    ->successNotificationTitle(fn ($record) => $record->title.' document has been updated.'),


                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete Document')
                    ->hiddenLabel()
                    ->color('danger')
                    ->size('lg')
                    ->closeModalByClickingAway(false)
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading('Delete Document?'),


            ], '')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('bulk_edit_documents')
                        ->slideOver()
                        ->modalFooterActionsAlignment('end')
                        ->modalSubmitActionLabel('Save Changes')
                        ->label('Bulk Edit Documents')
                        ->icon('heroicon-o-pencil-square')
                        ->modalHeading('Edit Selected Documents')
                        ->form(fn ($records) => [
                            Forms\Components\Repeater::make('documents')
                                ->label('Documents')
                                ->schema([
                                    Forms\Components\TextInput::make('id')->hidden(),

                                    Forms\Components\Textarea::make('title')
                                        ->label('Title')
                                        ->required(),

                                    Forms\Components\Textarea::make('description')
                                        ->label('Description')
                                        ->nullable(),

                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('Upload File')
                                        ->directory('student-documents')
                                        ->nullable(),
                                ])
                                ->columns(3)
                                ->default(
                                    collect($records)->map(fn ($record) => [
                                        'id' => $record->id,
                                        'title' => $record->title,
                                        'description' => $record->description,
                                    ])->toArray()
                                )
                                ->itemLabel(
                                    fn (array $state): ?string => strtoupper($state['title'] ?? '')
                                )
                                ->deletable(false)
                                ->reorderableWithDragAndDrop(false)
                                ->collapsible()
                                ->addable(false)
                                ->columnSpanFull()
                        ])
                        ->action(function (array $data) {
                            foreach ($data['documents'] as $doc) {
                                StudentDocument::where('id', $doc['id'])->update([
                                    'title' => $doc['title'],
                                    'description' => $doc['description'],
                                    'file_path' => $doc['file_path'] ?? null,
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Documents updated successfully!'),


                    BulkAction::make('bulk_upload_documents')
                        ->slideOver()
                        ->closeModalByClickingAway(false)
                        ->modalFooterActionsAlignment('end')
                        ->modalSubmitActionLabel('Save Changes')
                        ->label('Bulk Upload Documents')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->modalHeading('Bulk Upload Documents')
                        ->form(fn ($records) => [
                            Forms\Components\Repeater::make('documents')
                                ->columns(1)
                                ->label('Documents')
                                ->schema([
                                    Forms\Components\TextInput::make('id')->hidden(),

                                    Forms\Components\Textarea::make('title')
                                        ->label('Title')
                                        ->hidden()
                                        ->readOnly()
                                        ->required(),

                                    Forms\Components\Textarea::make('description')
                                        ->label('Description')
                                        ->hidden()
                                        ->readOnly()
                                        ->nullable(),

                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('Upload File')
                                        ->directory('student-documents')
                                        ->previewable()
                                        ->disk('public')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->nullable(),
                                ])
                                ->default(function () use ($records) {
                                    return collect($records)->map(fn ($record) => [
                                        'id' => $record->id,
                                        'title' => $record->title,
                                        'description' => $record->description,
                                    ])->values()->all();
                                })
                                ->itemLabel(
                                    fn (array $state): ?string => strtoupper($state['title'] ?? '')
                                )
                                ->deletable(false)
                                ->reorderableWithDragAndDrop(false)
                                ->collapsible()
                                ->addable(false)
                                ->columnSpanFull()
                        ])
                        ->action(function (array $data) {
                            if (!is_array($data['documents'] ?? null)) {
                                return;
                            }

                            foreach ($data['documents'] as $doc) {
                                $filePath = $doc['file_path'] ?? null;

                                if ($filePath instanceof UploadedFile) {
                                    $filePath = $filePath->store('student-documents', 'public');
                                }

                                StudentDocument::where('id', $doc['id'])->update([
                                    'title' => $doc['title'],
                                    'description' => $doc['description'],
                                    'file_path' => $filePath,
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Confirmation')
                                ->body('Documents have been successfully updated.')
                        ),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading('No documents found.')
            ->emptyStateDescription(null);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Repeater::make('documents')
                //     ->label('Documents')
                //     ->schema([
                //         Forms\Components\TextInput::make('title')
                //             ->required()
                //             ->maxLength(150),

                //         Forms\Components\Textarea::make('description')
                //             ->maxLength(255),

                //         Forms\Components\FileUpload::make('file_path')
                //             ->label('File')
                //             ->disk('public')
                //             ->directory('student-documents')
                //             ->required()
                //             ->previewable()
                //             ->acceptedFileTypes(['application/pdf', 'image/*']),
                //     ])
                //     ->minItems(1)
                //     ->defaultItems(1)
                //     ->addActionLabel('Add Document'),

            ]);
    }


}
