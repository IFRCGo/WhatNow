<?php

namespace App\Classes\RcnApi\Importer;
use App\Models\EventType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class BulkUploadTemplateExport implements FromArray, ShouldAutoSize, WithEvents
{
    /**
     * Constructor to initialize the export template
     */
    private $nationalSociety;
    private $region;
    private $headings = [
        'Title', 'Description', 'URL', 'Hazard', 'Urgency Level', 'Safety Message'
    ];
    private $urgencyLevels = '"Immediate,Warning,Anticipated,Assess and Plan,Mitigate Risks,Prepare to Respond,Recover"';

    private $eventTypesDropdown = [];

    private $data;

    public function __construct(string $nationalSociety, string $region,array $data, int $maxSupportingMessages)
    {
        $eventTypes = EventType::whereNotIn('code', ['other'])->get()->toArray();
        $this->nationalSociety = $nationalSociety;
        $this->eventTypesDropdown = '"' . implode(',', array_map(function ($event) {
                return "{$event['name']}";
            }, $eventTypes)) . '"';
        $this->region = $region;
        $this->data = $data;
        for($i = 0; $i< $maxSupportingMessages; $i++){
            $this->headings[] = 'Supporting Message ' . ($i + 1);
        }

    }


    /**
     * Define the data rows for the export
     */
    public function array(): array
    {
        return array_merge(
            [
                ['National Society', 'Region'],
                [$this->nationalSociety, $this->region], // Row 1-2
                $this->headings // Row 3
            ],
            $this->data // Insert dynamic data starting from Row 4
        );
    }

    /**
     * Event to add dropdowns
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 🔹 Dropdown for Urgency Level in E4
                $validationUrgency = $sheet->getCell('E4')->getDataValidation();
                $validationUrgency->setType(DataValidation::TYPE_LIST);
                $validationUrgency->setErrorStyle(DataValidation::STYLE_STOP);
                $validationUrgency->setAllowBlank(false);
                $validationUrgency->setShowInputMessage(true);
                $validationUrgency->setShowErrorMessage(true);
                $validationUrgency->setShowDropDown(true);
                $validationUrgency->setFormula1($this->urgencyLevels);
                $sheet->getCell('E4')->setDataValidation(clone $validationUrgency);

                // 🔹 Dropdown for Event Types in D4
                $validationEvent = $sheet->getCell('D4')->getDataValidation();
                $validationEvent->setType(DataValidation::TYPE_LIST);
                $validationEvent->setErrorStyle(DataValidation::STYLE_STOP);
                $validationEvent->setAllowBlank(false);
                $validationEvent->setShowInputMessage(true);
                $validationEvent->setShowErrorMessage(true);
                $validationEvent->setShowDropDown(true);
                $validationEvent->setFormula1($this->eventTypesDropdown);
                $sheet->getCell('D4')->setDataValidation(clone $validationEvent);
            }
        ];
    }
}
