<?php

namespace App\Classes\RcnApi\Importer;
use App\Models\EventType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Namshi\JOSE\Base64\Base64Encoder;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;
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
            $this->data
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

                $validationUrgency = $sheet->getCell('E4')->getDataValidation();
                $validationUrgency->setType(DataValidation::TYPE_LIST);
                $validationUrgency->setErrorStyle(DataValidation::STYLE_STOP);
                $validationUrgency->setAllowBlank(false);
                $validationUrgency->setShowInputMessage(true);
                $validationUrgency->setShowErrorMessage(true);
                $validationUrgency->setShowDropDown(true);
                $validationUrgency->setFormula1($this->urgencyLevels);
                $sheet->getCell('E4')->setDataValidation(clone $validationUrgency);

                $validationEvent = $sheet->getCell('D4')->getDataValidation();
                $validationEvent->setType(DataValidation::TYPE_LIST);
                $validationEvent->setErrorStyle(DataValidation::STYLE_STOP);
                $validationEvent->setAllowBlank(false);
                $validationEvent->setShowInputMessage(true);
                $validationEvent->setShowErrorMessage(true);
                $validationEvent->setShowDropDown(true);
                $validationEvent->setFormula1($this->eventTypesDropdown);
                $sheet->getCell('D4')->setDataValidation(clone $validationEvent);


                $maxColumnRow1 = Coordinate::stringFromColumnIndex(2);
                $row1Range = 'A1:' . $maxColumnRow1 . '1';

                $maxColumnRow3 = Coordinate::stringFromColumnIndex(count($this->headings));
                $row3Range = 'A3:' . $maxColumnRow3 . '3';

                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ff6b6b'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                $sheet->getStyle($row1Range)->applyFromArray($headerStyle);
                $sheet->getStyle($row3Range)->applyFromArray($headerStyle);

                $protection = $sheet->getProtection();
                $protection->setSheet(true);
                $base = new Base64Encoder();
                $protection->setPassword($base->encode('fsjsD-1FfJTZvs2X'));

                $sheet->getStyle('A1:Z100')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                $cellsToLock = ['A1', 'B1', 'A2', 'B2', 'A3:F3'];
                foreach ($cellsToLock as $cell) {
                    $sheet->getStyle($cell)->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                }
            }
        ];
    }
}
