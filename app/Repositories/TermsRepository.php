<?php

namespace App\Repositories;

use App\Models\Terms;

class TermsRepository extends Repository implements TermsRepositoryInterface
{
    
    const MODEL = Terms::class;

    
    public function getLatest()
    {
        return (self::MODEL)::latest()->first();
    }

    
    public function getLatestTermsVersion()
    {
        $latest = (self::MODEL)::latest()->first();

        if (!$latest) {
            return "1.0";
        }

        return $latest->version;
    }

    
    public function create(float $version, string $content, int $userId)
    {
        $terms = new Terms([
            'version' => $version,
            'content' => $content,
            'user_id' => $userId
        ]);
        $terms->save();

        return $terms;
    }

    
    public function getOrderedByLatest()
    {
        return $this->query()->with('user')->latest();
    }
}
