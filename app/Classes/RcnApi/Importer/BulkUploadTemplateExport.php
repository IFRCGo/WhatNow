<?php

namespace App\Classes\RcnApi\Importer;

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
        'Title', 'Description', 'URL', 'Hazard', 'Urgency Level', 'Key Message',
        'Supporting Message 1', 'Supporting Message 2', 'Supporting Message 3'
    ];
    private $urgencyLevels = '"Immediate,Warning,Anticipated,Assess and Plan,Mitigate Risks,Prepare to Respond,Recover"';

    public function __construct(string $nationalSociety, string $region)
    {
        $this->nationalSociety = $nationalSociety;
        $this->region = $region;
    }

    /**
     * Define the data rows for the export
     */
    public function array(): array
    {
        return [
            ['National Society', 'Region'],
            [$this->nationalSociety, $this->region], // Row 1
            $this->headings // Row 3
        ];
    }

    /**
     * Event to add dropdown only in cell E4
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Create dropdown validation for cell E4
                $validation = $sheet->getCell('E4')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1($this->urgencyLevels);

                // Ensure validation is applied correctly
                $sheet->getCell('E4')->setDataValidation(clone $validation);
            }
        ];
    }
}
