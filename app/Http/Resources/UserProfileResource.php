<?php

namespace App\Http\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Resources\Json\Resource;

class UserProfileResource extends Resource
{
    
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country_code' => $this->country_code,
            'display_name' => $this->user->name,
            'organisation' => $this->organisation,
            'industry_type' => $this->industry_type,
            'terms_version' => $this->terms_version,
            'accepted_agreement' => $this->accepted_agreement,
            'api_used_in' => $this->api_used_in,
            'photo_url' => $this->getPhotoUrlFromEmail($this->user->email)
        ];
    }

    
    private function getPhotoUrlFromEmail($email)
    {
        $http = new Client();
        try {
            $gravatarId = md5(strtolower($email));
            $http->head("http://www.gravatar.com/avatar/$gravatarId?d=404");
            return "https://www.gravatar.com/avatar/$gravatarId.jpg?s=200&d=mm";
        } catch (TransferException $e) {
            return null;
        }
    }
}
