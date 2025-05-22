<?php

namespace App\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;

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
        return $table
            ->deferLoading()
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')

                    ->searchable(),

                Tables\Columns\IconColumn::make('file_path')
                    ->alignCenter()
                    ->url(fn ($record): string => $record->file_path)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->size('md')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->label('View/Download'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date uploaded')
                    ->dateTime('M d, Y - h:i A'),


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
                                    foreach ($data['documents'] as $doc) {
                                        $livewire->getRelationship()->create([
                                            'title' => $doc['title'],
                                            'description' => $doc['description'] ?? null,
                                            'file_path' => $doc['file_path'],
                                        ]);
                                    }
                                })
                                ->createAnother(false)
                                ->successNotification(Notification::make()->success()->title('Documents uploaded successfully.'))

                        ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                                ->tooltip('Delete Document')
                                ->hiddenLabel()
                                ->color('danger')
                                ->link()
                                ->closeModalByClickingAway(false)
                                ->icon('heroicon-o-x-circle')
                                ->modalHeading('Delete Document?'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
