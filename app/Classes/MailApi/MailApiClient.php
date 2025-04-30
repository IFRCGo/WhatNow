<?php

namespace App\Classes\MailApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Classes\MailApi\Contract\MailApiClientRequest;


class MailApiClient
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(MailApiClientRequest $request)
    {
        try {
            $json = $request->jsonSerialize();
            $response = $this->client->post(config('mail.endpoint_url'), [
                'json' => $json
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception("Error sending email via API: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
