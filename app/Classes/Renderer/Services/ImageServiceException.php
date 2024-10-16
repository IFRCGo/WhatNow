<?php

namespace App\Classes\Renderer\Services;

class ImageServiceException extends \Exception
{
    public function __construct($message, $id = null)
    {
        if ($id !== null) {
            $message .= " ($id)";
        }
        parent::__construct($message);
    }

    public static function missingParameter(string $param)
    {
        return new self("Invalid parameter", $param);
    }

    public static function missingInstruction(int $id)
    {
        return new self("Instruction not found", $id);
    }

    public static function missingTranslations(string $code)
    {
        return new self("No translations found for translation code", $code);
    }

    public static function invalidStageReference(string $ref)
    {
        return new self("Invalid stage reference", $ref);
    }

    public static function missingStages(string $ref)
    {
        return new self("No items found for stage reference", $ref);
    }
}
