<?php

namespace App\Classes\RcnApi\Importer;
use App\Models\EventType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Namshi\JOSE\Base64\Base64Encoder;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BulkUploadTemplateExport implements WithMultipleSheets
{
    private $nationalSociety;
    private $region;
    private $data;
    private $maxSupportingMessages;

    public function __construct(string $nationalSociety, string $region, array $data, int $maxSupportingMessages)
    {
        $this->nationalSociety = $nationalSociety;
        $this->region = $region;
        $this->data = $data;
        $this->maxSupportingMessages = $maxSupportingMessages;
    }

    public function sheets(): array
    {
        return [
            new MainSheet($this->nationalSociety, $this->region, $this->data, $this->maxSupportingMessages),
            new ImageSheet()
        ];
    }
}

class MainSheet implements FromArray, ShouldAutoSize, WithEvents
{
    private $nationalSociety;
    private $region;
    private $headings = [
        'Title', 'Description', 'URL', 'Hazard', 'Urgency Level', 'Safety Message'
    ];
    private $urgencyLevels = '"Immediate,Warning,Anticipated,Assess and Plan,Mitigate Risks,Prepare to Respond,Recover"';
    private $eventTypesDropdown = [];
    private $data;

    public function __construct(string $nationalSociety, string $region, array $data, int $maxSupportingMessages)
    {
        $eventTypes = EventType::whereNotIn('code', ['other'])->get()->toArray();
        $this->nationalSociety = $nationalSociety;
        $this->region = $region;
        $this->data = $data;
        $this->eventTypesDropdown = '"' . implode(',', array_map(fn($event) => "{$event['name']}", $eventTypes)) . '"';

        for ($i = 0; $i < $maxSupportingMessages; $i++) {
            $this->headings[] = 'Supporting Message ' . ($i + 1);
        }
    }

    public function array(): array
    {
        return array_merge(
            [['National Society', 'Region']],
            [[$this->nationalSociety, $this->region]],
            [$this->headings],
            $this->data
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $validationUrgency = $sheet->getCell('E4')->getDataValidation();
                $validationUrgency->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setFormula1($this->urgencyLevels);
                $sheet->getCell('E4')->setDataValidation(clone $validationUrgency);

                $validationEvent = $sheet->getCell('D4')->getDataValidation();
                $validationEvent->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setFormula1($this->eventTypesDropdown);
                $sheet->getCell('D4')->setDataValidation(clone $validationEvent);

                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'ff6b6b']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ];

                $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);
                $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);

                // Protección de celdas
                $protection = $sheet->getProtection();
                $protection->setSheet(true);
                $protection->setPassword((new Base64Encoder())->encode('fsjsD-1FfJTZvs2X'));

                $sheet->getStyle('A1:Z100')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                $cellsToLock = ['A1', 'B1', 'A2', 'B2', 'A3:F3'];
                foreach ($cellsToLock as $cell) {
                    $sheet->getStyle($cell)->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                }
            }
        ];
    }
}

class ImageSheet implements WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $drawing = new Drawing();
                $drawing->setName('Template Guide');
                $drawing->setDescription('How the template works');
                $drawing->setPath(public_path('images/how_the_template_works.png'));
                $drawing->setHeight(100);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
            }
        ];
    }
}
