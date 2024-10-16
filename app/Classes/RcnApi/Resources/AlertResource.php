<?php

namespace App\Classes\RcnApi\Resources;

use Illuminate\Support\Collection;
use App\Classes\RcnApi\Entities\Alert;

class AlertResource extends AbstractResource
{
    
    public function getAlertsForOrganisation(string $orgCode)
    {
        return $this->handleApiCall(function () use ($orgCode) {
            $response = $this->http->get('org/' . $orgCode . '/alerts');
            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Alert::createFromArray($item);
            }, $contents['data']));
        });
    }

    public function getByIdentifier($identifier)
    {
        return $this->handleApiCall(function () use ($identifier) {
            $response = $this->http->get('alerts/' . $identifier);
            $contents = json_decode($response->getBody()->getContents(), true);

            return Alert::createFromArray($contents['data']);
        });
    }
}
