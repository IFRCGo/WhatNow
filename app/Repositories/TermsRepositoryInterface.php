<?php

namespace App\Repositories;

use App\Models\Terms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface TermsRepositoryInterface
{
    
    public function getLatest();

    
    public function getLatestTermsVersion();

    
    public function create(float $version, string $content, int $userId);

    
    public function getOrderedByLatest();
}
