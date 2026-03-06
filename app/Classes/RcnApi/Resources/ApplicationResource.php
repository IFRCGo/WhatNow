<?php

namespace App\Classes\RcnApi\Resources;

use App\Classes\RcnApi\Entities\Application;
use Illuminate\Support\Collection;

class ApplicationResource extends AbstractResource
{

    public function getApplicationsForUser(int $userId)
    {
        return $this->handleApiCall(function () use ($userId) {
            $response = $this->http->get('apps?userId=' . $userId);
            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Application::createFromArray($item);
            }, $contents['data']));
        });
    }


    public function getAppById(int $id)
    {
        return $this->handleApiCall(function () use ($id) {
            $response = $this->http->get('apps/' . $id);
            $contents = json_decode($response->getBody()->getContents(), true);

            return Application::createFromArray($contents['data']);
        });
    }


    public function createApplication(string $name, string $description = null, int $estimatedUsers = 0, int $userId)
    {
        return $this->handleApiCall(function () use ($name, $description, $estimatedUsers, $userId) {
            $response = $this->http->post('apps', [
                'json' => [
                    'name' => $name,
                    'description' => $description,
                    'estimatedUsers' => $estimatedUsers,
                ],
                'query' => ['userId' => $userId],
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);

            return Application::createFromArray($contents['data']);
        });
    }


    public function updateApplication(int $id, int $estimatedUsers)
    {
        return $this->handleApiCall(function () use ($id, $estimatedUsers) {
            $response = $this->http->patch('apps/' . $id, [
                'json' => [
                    'estimatedUsers' => $estimatedUsers,
                ],
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);

            return Application::createFromArray($contents['data']);
        });
    }


    public function activateApplication(int $userid)
    {
        return $this->handleApiCall(function () use ($userid) {
            $response = $this->http->patch('apps/' . $userid . '/activate');
            $contents = json_decode($response->getBody()->getContents(), true);

            return Application::createFromArray($contents['data']);
        });
    }


    public function deactivateApplication(int $userid)
    {
        return $this->handleApiCall(function () use ($userid) {
            $response = $this->http->patch('apps/' . $userid . '/deactivate');
            $contents = json_decode($response->getBody()->getContents(), true);

            return Application::createFromArray($contents['data']);
        });
    }


    public function deleteApplication(int $id)
    {
        $this->handleApiCall(function () use ($id) {
            $this->http->delete('apps/' . $id);
        });
    }

    public function updateRules(int $tenantUserId, array $rules)
    {
        return $this->handleApiCall(function () use ($tenantUserId, $rules) {
            $response = $this->http->post('applications/rules', [
                'json' => [
                    'tenant_user_id' => (string) $tenantUserId,
                    'rules' => $rules,
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        });
    }
}
