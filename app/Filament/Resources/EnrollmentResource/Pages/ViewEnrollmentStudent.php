<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Models\Requirement;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ViewEnrollmentStudent extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    //protected static ?string $title = 'Verify document';

    public function getSubheading(): Htmlable|string|null
    {
        return new HtmlString('<span class="font-semibold text-primary">'.$this->getRecord()->reference_number.'</span>');
    }

    public function getBreadcrumb(): string
    {
        return 'Verify document';
    }

    /**s
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return 'Document Verification';
    }

    public function getRecordTitle(): string|Htmlable
    {
        return 'Reference No: '.$this->getRecord()->reference_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_verification')
                ->hidden(fn (Enrollment $record): bool => $record->status_key === 'enrolled')
                ->label('Confirm Documents')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->modalWidth('5xl')
                ->slideOver()
                ->modalAlignment('center')
                ->closeModalByClickingAway(false)
                ->modalFooterActionsAlignment('end')
                ->form([
                    CheckboxList::make('requirements_presented')
                        ->gridDirection('row')
                        ->searchable()
                        ->columns(4)
                        ->extraInputAttributes(['class' => 'text-sm'])
                        ->label('Requirements presented')
                        ->options(Requirement::all()->pluck('document_name', 'id')->toArray())
                        ->descriptions(
                            Requirement::all()
                                ->pluck('document_description', 'id')
                                ->map(fn ($desc) => Str::limit($desc, 30))
                                ->toArray()
                        ),

                    Placeholder::make('separator')
                        ->hiddenLabel()
                        ->content(new HtmlString('
                            <div class="flex items-center max-w-xs mx-auto my-4 text-uppercase">
                                <hr class="flex-grow border-t-2 border-gray-700 rounded" style="height: 3px;" />
                                    <span class="mx-2 font-semibold text-gray-500">&nbsp; OR  &nbsp;</span>
                                <hr class="flex-grow border-t-2 border-gray-700 rounded" style="height: 3px;" />
                            </div>
                        '))
                        ->columnSpan('full'),

                    Repeater::make('studentDocuments')
                        ->hiddenLabel()
                        ->label('Other Documents presented')
                        ->addActionLabel('Add other document')
                        ->defaultItems(0)
                        ->columnSpanFull()
                        ->reorderableWithDragAndDrop(false)
                        ->simple(
                            TextInput::make('title')
                                ->placeholder('Enter document title')
                                ->required(),
                        ),
                ])
                ->action(function (array $data, Enrollment $record) {
                    // 1. Save checked requirements as student documents
                    foreach ($data['requirements_presented'] as $requirementId) {
                        $requirement = Requirement::find($requirementId);
                        $record->documents()->firstOrCreate([
                            'title' => $requirement->document_name,
                        ], [
                            'description' => $requirement->document_description,
                        ]);
                    }

                    // 2. Save any extra manually added documents
                    foreach ($data['studentDocuments'] ?? [] as $doc) {
                        $record->documents()->create([
                            'title' => $doc['title'],
                            'description' => $doc['description'] ?? null,
                        ]);
                    }

                    $record->status_key = 'enrolled';
                    $record->save();

                    // Show success notification
                    Notification::make()
                        ->success()
                        ->title('Verification confirmed!')
                        ->send();

                    // Manually redirect (if on custom page)
                    return redirect()->route('filament.app.resources.enrollments.index');

                }),

            DeleteAction::make('delete')
                ->modalIcon('heroicon-o-archive-box-x-mark')
                ->modalHeading('Void this enrollment?')
                ->icon('heroicon-o-archive-box-x-mark')
                ->label('Void')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Transaction voided!')
                )
                ->before(function () {
                    $this->record->deleted_by = Auth::user()->id;
                    $this->record->save();

                })
        ];
    }

}
