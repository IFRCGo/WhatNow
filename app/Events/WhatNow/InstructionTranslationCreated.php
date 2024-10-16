<?php

namespace App\Events\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Events\Event;


class InstructionTranslationCreated extends Event
{
    
    public $instruction;

    
    public $translation;

    
    public function __construct(Instruction $instruction, InstructionTranslation $translation)
    {
        $this->instruction = $instruction;
        $this->translation = $translation;
    }
}
