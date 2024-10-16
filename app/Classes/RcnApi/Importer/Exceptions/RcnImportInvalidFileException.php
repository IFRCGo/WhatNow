<?php

namespace App\Classes\RcnApi\Importer\Exceptions;

use Throwable;

class RcnImportInvalidFileException extends RcnImportException
{
    
    protected $errorCode;

    public function __construct($errorCode, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, $code, $previous);
    }

    
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
