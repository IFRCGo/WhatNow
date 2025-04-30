<?php

namespace App\Classes\RcnApi\Resources;

use Illuminate\Support\Collection;
use League\Csv\Writer;

class UsageResource extends AbstractResource
{

    public function listApplicationRequestCount(string $fromDate, string $toDate, int $page = 0, bool $groupedByUser = false, $orderBy, $sort)
    {
        return $this->handleApiCall(function () use ($fromDate, $toDate, $page, $groupedByUser, $orderBy, $sort) {
            //only name, estimatedUsers, requestCount are used in the sort
            $orderBy = in_array($orderBy, ['name', 'estimatedUsers', 'requestCount']) ? $orderBy : 'name';
            $sort = $sort ?? 'asc';
            $url = $page !== 0 ? "usage/applications?fromDate={$fromDate}&toDate={$toDate}&page={$page}&orderBy={$orderBy}&sort={$sort}" :
                "usage/applications?fromDate={$fromDate}&toDate={$toDate}&orderBy={$orderBy}&sort={$sort}";

            $response = $this->http->get($url);
            $contents = json_decode($response->getBody()->getContents(), true);

            $applications = new Collection($contents['data']['data'] ?? []);

            $applicationsByUser = $applications->groupBy('tenant_user_id')->map(function ($applicationsByUser) {
                $userId = intval($applicationsByUser->first()['tenant_user_id']);

                $user = \App\Models\Access\User\User::with('userProfile')->find($userId);

                $countryCode = $user && is_object($user->userProfile) && property_exists($user->userProfile, 'country_code') ? $user->userProfile->country_code : 'N/A';
                $organisation = $user && is_object($user->userProfile) && property_exists($user->userProfile, 'organisation') ? $user->userProfile->organisation : 'N/A';
                $username = $user && is_object($user->userProfile) ? $user->userProfile->first_name . ' ' . $user->userProfile->last_name : 'N/A';

                return $applicationsByUser->map(function ($application) use ($countryCode, $organisation, $username) {
                    $application['location'] = $countryCode;
                    $application['organisation'] = $organisation;
                    $application['username'] = $username;

                    unset($application['tenant_user_id']);

                    return $application;
                });
            });

            if ($groupedByUser) {
                return $applicationsByUser;
            }

                        $contents['data']['data'] = $applicationsByUser->flatten(1);

            return $contents['data'];
        });
    }


    public function listEndpointRequestCount(string $fromDate, string $toDate, int $page = 0)
    {
        return $this->handleApiCall(function () use ($fromDate, $toDate, $page) {
            $url = $page !== 0 ? "usage/endpoints?fromDate={$fromDate}&toDate={$toDate}&page={$page}" :
                "usage/endpoints?fromDate={$fromDate}&toDate={$toDate}";

            $response = $this->http->get($url);
            $contents = json_decode($response->getBody()->getContents(), true);

            $endpoints = new Collection($contents['data']);

            return $endpoints->groupBy('application');
        });
    }


    public function exportApplicationUsageCsv(string $fromDate, string $toDate): string
    {
                $applicationsByUser = $this->listApplicationRequestCount($fromDate, $toDate, 0, true);

        $csv = Writer::createFromString('');

        $csv->insertOne([
            'Username',
            'Organisation',
            'Location',
            'Application Name',
            'Number of Hits',
            'Estimated Reach',
        ]);

        foreach ($applicationsByUser as $applications) {
            $index = 0;
            $applications->each(function ($application) use ($csv, &$index) {
                $csv->insertOne([
                    $index === 0 ? $application['username'] : '',
                    $application['organisation'],
                    $application['location'],
                    $application['name'],
                    $application['requestCount'],
                    $application['estimatedUsers'],
                ]);

                $index++;
            });
        }

        return $csv->toString();
    }


    public function exportEndpointUsageCsv(string $fromDate, string $toDate): string
    {
        $endpoints = $this->listEndpointRequestCount($fromDate, $toDate);
        $applications = collect();

        $csv = Writer::createFromString('');

        $csv->insertOne([
            'Organisation',
            'Application',
            'Endpoint',
            'Number of Hits',
        ]);

        $endpoints->each(function ($endpoint) use ($csv, $applications) {
            $endpoint->each(function ($application) use ($csv, $applications) {
                $userModel = \App\Models\Access\User\User::with('userProfile')->find($application['application_tenant_user_id']);
                $organisationName = $userModel ? $userModel->userProfile->organisation : 'N/A';

                $applications->push([
                    'organisation_name' => $organisationName,
                    'application_name' => $application['application_name'],
                    'endpoint' => $application['endpoint'],
                    'hit_count' => $application['hit_count'],
                ]);
            });
        });

        $applications->groupBy('organisation')->each(function ($organisation) use ($csv) {
            $index = 0;
            $organisation->sortBy('application_name')->each(function ($application) use ($csv, &$index) {
                $csv->insertOne([
                    $index === 0 ? $application['organisation_name'] : '',
                    $application['application_name'],
                    $application['endpoint'],
                    $application['hit_count'],
                ]);

                $index++;
            });
        });

        return $csv->toString();
    }


    public function getTotals($request): Collection
    {
        $arrRequest = explode('?', $request->fullUrl());
        $queryString = "";
        if (count($arrRequest) > 1) {
            $queryString = '?'.$arrRequest[1];
        }
        return $this->handleApiCall(function () use ($queryString) {
            $response = $this->http->get('usage/totals'.$queryString);
            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection($contents['data']);
        });
    }
}
