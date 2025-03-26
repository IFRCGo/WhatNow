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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
        $this->subnational = $region;
        $this->data = $data;
        if($maxSupportingMessages <= 0) $maxSupportingMessages = 3;
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
                [$this->nationalSociety, $this->subnational], // Row 1-2
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
                $sheet->setTitle("Bulk Upload Template");

                $guideSheet = $sheet->getParent()->createSheet();
                $guideSheet->setTitle("How template works");
                $drawing = new Drawing();
                $drawing->setName('How Template Works');
                $drawing->setDescription('How Template Works');
                $drawing->setPath(base_path('resources/assets/img/template.png'));
                $drawing->setHeight(1080);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($guideSheet);

                $hiddenSheet = $sheet->getParent()->createSheet();
                $hiddenSheet->setTitle('DropdownHazardData');


                $protection = $sheet->getProtection();
                $protection->setSheet(false);

                $urgencyLevelsArray = array_map(function ($item) {
                    return str_replace('"', '', trim($item));
                }, explode(',', $this->urgencyLevels));

                $eventTypesArray = array_map(function ($item) {
                    return str_replace('"', '', trim($item));
                }, explode(',', $this->eventTypesDropdown));

                if (empty($urgencyLevelsArray) || empty($eventTypesArray)) {
                    throw new \Exception('Dropdown lists cannot be empty.');
                }

                $row = 1;
                foreach ($eventTypesArray as $type) {
                    $hiddenSheet->setCellValue("A{$row}", $type);
                    $row++;
                }
                $eventRange = "DropdownHazardData!A1:A" . ($row - 1);
                $urgencyFormula = '"' . implode(',', $urgencyLevelsArray) . '"';

                $validationUrgency = new DataValidation();
                $validationUrgency->setType(DataValidation::TYPE_LIST);
                $validationUrgency->setErrorStyle(DataValidation::STYLE_STOP);
                $validationUrgency->setAllowBlank(true);
                $validationUrgency->setShowInputMessage(true);
                $validationUrgency->setShowErrorMessage(true);
                $validationUrgency->setShowDropDown(true);
                $validationUrgency->setErrorTitle('Select a valid urgency level');
                $validationUrgency->setError('Value is not in list.');
                $validationUrgency->setPromptTitle('Pick from list');
                $validationUrgency->setPrompt('Please pick a Urgency Level from the drop-down list.');
                $validationUrgency->setFormula1($urgencyFormula);

                $validationEvent = new DataValidation();
                $validationEvent->setType(DataValidation::TYPE_LIST);
                $validationEvent->setErrorStyle(DataValidation::STYLE_STOP);
                $validationEvent->setAllowBlank(true);
                $validationEvent->setShowInputMessage(true);
                $validationEvent->setShowErrorMessage(true);
                $validationEvent->setShowDropDown(true);
                $validationEvent->setErrorTitle('Select a valid hazard level');
                $validationEvent->setError('Value is not in list.');
                $validationEvent->setPromptTitle('Pick from list');
                $validationEvent->setPrompt('Please pick a Hazard from the drop-down list.');
                $validationEvent->setFormula1($eventRange);

                for ($i = 4; $i <= 100; $i++) {
                    $event->sheet->getCell("E{$i}")->setDataValidation(clone $validationUrgency);
                    $event->sheet->getCell("D{$i}")->setDataValidation(clone $validationEvent);
                }


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

                $sheet->getStyle('A1:Z100')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                $sheet->getStyle('A1')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('B1')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('A2')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('B2')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('A3')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('B3')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('C3')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('D3')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle('E3')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getProtection()->setSort(true);
                $sheet->getProtection()->setInsertRows(true);
                $sheet->getProtection()->setInsertColumns(true);
                $sheet->getProtection()->setDeleteRows(true);
                $sheet->getProtection()->setDeleteColumns(true);
                $sheet->getProtection()->setFormatColumns(false);
                $sheet->getProtection()->setFormatRows(false);
                $sheet->getProtection()->setSheet(true);
                $base = new Base64Encoder();
                $protection->setPassword($base->encode('fsjsD-1FfJTZvs2X'));

                $hiddenSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);



            }
        ];
    }
}
