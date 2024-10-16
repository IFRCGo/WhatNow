<?php


namespace App\Classes\RcnApi\Resources;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use Illuminate\Support\Collection;

interface WhatNowResourceInterface
{
    
    public function getOrganisations();

    
    public function getOrganisationByCountryCode(string $countryCode);

    
    public function updateOrganisationByCountryCode(Organisation $organisation);

    
    public function getPublishedInstructionsByCountryCode(string $countryCode): Collection;

    
    public function getLatestInstructionsByCountryCode(string $countryCode): Collection;

    
    public function getInstruction(int $id): Instruction;

    
    public function getLatestInstructionRevision(int $id): Instruction;

    
    public function createInstruction(Instruction $instruction): Instruction;

    
    public function updateInstruction(Instruction $instruction): Instruction;

    
    public function createTranslation($id, InstructionTranslation $translation): Instruction;

    
    public function patchTranslation($id, $translationId, $patch);

    
    public function publishTranslations($ids);

    
    public function deleteInstruction(int $id): void;
}
