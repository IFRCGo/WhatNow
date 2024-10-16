<?php

namespace App\Events\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Events\Event;


class InstructionTranslationUnpublished extends Event
{
    
    public $instruction;

    
    public $languageCode;

    
    public function __construct(Instruction $instruction, string $languageCode)
    {
        $this->instruction = $instruction;
        $this->languageCode = $languageCode;
    }
}
