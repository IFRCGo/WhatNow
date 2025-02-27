<?php

namespace App\Repositories\Access\User;

use App\Models\Access\User\UserProfile;
use App\Repositories\Access\UserRepository;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;


class UserProfileRepository extends Repository
{
    
    const MODEL = UserProfile::class;

    
    protected $users;


    public function create(int $userId, $data)
    {
        $userProfileData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'accepted_agreement' => true,
        ];

        if (isset($data['country_code'])) {
            $userProfileData['country_code'] = $data['country_code'];
        }

        if (isset($data['organisation'])) {
            $userProfileData['organisation'] = $data['organisation'];
        }

        if (isset($data['industry_type'])) {
            $userProfileData['industry_type'] = $data['industry_type'];
        }

        if (isset($data['api_used_in'])) {
            $userProfileData['api_used_in'] = $data['api_used_in'];
        }
        if (isset($data['terms_version'])) {
            $userProfileData['terms_version'] = $data['terms_version'];
        }


        
        $userProfile = new UserProfile($userProfileData);
        $userProfile->user()->associate($userId);
        $userProfile->saveOrFail();

        return $userProfile;
    }

    
    public function update(Model $userProfile, array $data)
    {
        if (isset($data['first_name'])) {
            $userProfile->first_name = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $userProfile->last_name = $data['last_name'];
        }

        if (isset($data['country_code'])) {
            $userProfile->country_code = $data['country_code'];
        }

        if (isset($data['organisation'])) {
            $userProfile->organisation = $data['organisation'];
        }

        if (isset($data['industry_type'])) {
            $userProfile->industry_type = $data['industry_type'];
        }

        if (isset($data['api_used_in'])) {
            $userProfile->api_used_in = $data['api_used_in'];
        }

        if (isset($data['terms_version'])) {
            $userProfile->terms_version = $data['terms_version'];
        }

        if (isset($data['accepted_agreement'])) {
            $userProfile->accepted_agreement = $data['accepted_agreement'];
        }

        $userProfile->save();
    }
}
