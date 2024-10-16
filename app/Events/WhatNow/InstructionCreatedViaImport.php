<?php

namespace App\Events\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Events\Event;


class InstructionCreatedViaImport extends Event
{
    
    public $instruction;

    
    public function __construct(Instruction $instruction)
    {
        $this->instruction = $instruction;
    }
}
