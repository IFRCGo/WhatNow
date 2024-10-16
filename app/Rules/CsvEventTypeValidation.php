<?php

namespace App\Rules;

use App\Classes\RcnApi\Importer\RcnImporter;
use App\Models\EventType;
use Illuminate\Contracts\Validation\Rule;
use League\Csv\Reader;

class CsvEventTypeValidation implements Rule
{

    private $errors = [];

    
    public function passes($attribute, $value)
    {
        $csv = Reader::createFromPath($value->path(), 'r');
        $csv->setHeaderOffset(RcnImporter::INSTRUCTION_HEADERS_OFFSET);
        $records = $csv->getRecords();
        $validEventTypes = EventType::all();

        foreach ($records as $offset => $record) {
            if ($offset < RcnImporter::INSTRUCTION_HEADERS_OFFSET) {
                continue;             }

            $eventType = $record[trans('csvTemplate.instruction_columns.eventType')];
            $otherType = $record[trans('csvTemplate.instruction_columns.otherType')];

            if ($otherType === 'No' && is_null($validEventTypes->where('name', $eventType)->first())) {
                $this->errors[] = $eventType;
            }
        }

        if(count($this->errors) > 0)
        {
            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    
    public function message()
    {
        return 'The following Hazard Event Types are invalid: ' . implode(', ', $this->errors);
    }
}
