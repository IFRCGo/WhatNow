<?php

namespace App\Events\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Events\Event;


class InstructionUpdatedViaImport extends Event
{
    
    public $instruction;

    
    private $forced;

    
    public function __construct(Instruction $instruction, bool $forced = false)
    {
        $this->instruction = $instruction;
        $this->forced;
    }

    
    public function wasForced()
    {
        return $this->forced;
    }
}
