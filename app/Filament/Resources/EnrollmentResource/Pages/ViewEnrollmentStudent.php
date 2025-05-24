<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Models\Enrollment;
use App\Models\Requirement;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\EnrollmentResource;

use function Illuminate\Log\log;

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
                        ->options(fn () => $this->getRequirementOptions())
                        ->descriptions(fn () => $this->getRequirementDescriptions()),

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
                    $record->verifyAndSaveDocuments($data);

                    Notification::make()
                        ->success()
                        ->title('Confirmation')
                        ->body('Enrollment (<b>'.strtoupper($record->reference_number).'</b>) was successfully verified.')
                        ->send();

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



    protected function getRequirementOptions(): array
    {
        $record = $this->getRecord();

        if (! $record || ! $record->student) {
            return [];
        }
        $presented = $this->getRecord()->studentDocuments->pluck('title');

        return \App\Models\Requirement::whereNotIn('document_name', $presented)
            ->pluck('document_name', 'id')
            ->toArray();


    }

    protected function getRequirementDescriptions(): array
    {
        $record = $this->getRecord();

        if (! $record || ! $record->student) {
            return [];
        }

        $presented = $this->getRecord()->studentDocuments->pluck('title')->toArray();

        return \App\Models\Requirement::whereNotIn('document_name', $presented)
            ->pluck('document_description', 'id')
            ->map(fn ($desc) => \Illuminate\Support\Str::limit($desc, 30))
            ->toArray();
    }


}
