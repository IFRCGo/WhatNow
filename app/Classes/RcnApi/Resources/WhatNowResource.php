<?php

namespace App\Classes\RcnApi\Resources;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use Illuminate\Support\Collection;

class WhatNowResource extends AbstractResource implements WhatNowResourceInterface
{

    public function getOrganisationByCountryCode(string $countryCode)
    {
        return $this->handleApiCall(function () use ($countryCode) {
            $response = $this->http->get('org/'.$countryCode);

            $contents = json_decode($response->getBody()->getContents(), true);

            return Organisation::createFromArray($contents['data']);
        });
    }


    public function getOrganisations()
    {
        return $this->handleApiCall(function () {
            $response = $this->http->get('org/');

            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Organisation::createFromArray($item);
            }, $contents['data']));
        });
    }


    public function updateOrganisationByCountryCode(Organisation $organisation)
    {
        return $this->handleApiCall(function () use ($organisation) {
            $response = $this->http->put('org/'.$organisation->getCountryCode(), [
                'json' => $organisation,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            return Organisation::createFromArray($contents['data']);
        });
    }


    public function getPublishedInstructionsByCountryCode(string $countryCode): Collection
    {
        return $this->handleApiCall(function () use ($countryCode) {
            $response = $this->http->get('org/' . $countryCode . '/whatnow');

            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Instruction::createFromResponse($item);
            }, $contents['data']));
        });
    }


    public function getLatestInstructionsByCountryCode(string $countryCode): Collection
    {
        return $this->handleApiCall(function () use ($countryCode) {
            $response = $this->http->get('org/' . $countryCode . '/whatnow/revisions/latest');

            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Instruction::createFromResponse($item);
            }, $contents['data']));
        });
    }


    public function getAllInstructions(): Collection
    {
        return $this->handleApiCall(function () {
            $response = $this->http->get('org/whatnow/revisions/all');

            $contents = json_decode($response->getBody()->getContents(), true);

            return new Collection(array_map(function (array $item) {
                return Instruction::createFromResponse($item);
            }, $contents['data']));
        });
    }


    public function getInstruction(int $id): Instruction
    {
        return $this->handleApiCall(function () use ($id) {
            $response = $this->http->get('whatnow/' . $id);
            $contents = json_decode($response->getBody()->getContents(), true);

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function getLatestInstructionRevision(int $id): Instruction
    {
        return $this->handleApiCall(function () use ($id) {
            $response = $this->http->get('whatnow/' . $id . '/revisions/latest');
            $contents = json_decode($response->getBody()->getContents(), true);

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function createInstruction(Instruction $instruction): Instruction
    {
        return $this->handleApiCall(function () use ($instruction) {
            $response = $this->http->post('whatnow/', [
                'json' => $instruction,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function updateInstruction(Instruction $instruction): Instruction
    {
        if (! $instruction->getId()) {
            throw new RcnApiException('Instruction request has no id');
        }

        return $this->handleApiCall(function () use ($instruction) {
            $response = $this->http->put('whatnow/' . $instruction->getId(), [
                'json' => $instruction,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            if (! $contents['data']) {
                throw new RcnApiException('Instruction request has no data');
            }

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function createTranslation($id, InstructionTranslation $translation): Instruction
    {
        return $this->handleApiCall(function () use ($id, $translation) {
            $response = $this->http->post('whatnow/'.$id.'/revisions', [
                'json' => $translation,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function patchTranslation($id, $translationId, $patch)
    {
        return $this->handleApiCall(function () use ($id, $translationId, $patch) {
            $response = $this->http->patch('whatnow/' . $id . '/revisions/' . $translationId, [
                'json' => $patch,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            return Instruction::createFromResponse($contents['data']);
        });
    }


    public function publishTranslations($ids)
    {
        $this->handleApiCall(function () use ($ids) {
            $this->http->post('whatnow/publish', [
                'json' => ['translationIds' => $ids],
            ]);
        });
    }


    public function deleteInstruction(int $id): void
    {
        $this->handleApiCall(function () use ($id) {
            $this->http->delete('whatnow/' . $id);
        });
    }

    public function getAllForOrganisation(string $country_code)
    {
        return $this->handleApiCall(function () use ($country_code) {
            $response = $this->http->get('regions/' . $country_code);

            return json_decode($response->getBody()->getContents(), true);
        });
    }

    public function getForCountryCode(string $country_code, string $code)
    {
        return $this->handleApiCall(function () use ($country_code, $code) {
            $response = $this->http->get("regions/$country_code/$code");

            return json_decode($response->getBody()->getContents(), true);
        });
    }

    public function createRegion(array $input)
    {
        return $this->handleApiCall(function () use ($input) {
            $response = $this->http->post('regions', ['json' => $input]);

            return json_decode($response->getBody()->getContents(), true);
        });
    }

    public function updateRegion(int $regionId, array $input)
    {
        return $this->handleApiCall(function () use ($regionId, $input) {
            $response = $this->http->put("regions/region/$regionId", ['json' => $input]);

            return json_decode($response->getBody()->getContents(), true);
        });
    }

    public function deleteRegion(int $regionId)
    {
        $this->handleApiCall(function () use ($regionId) {
            $response = $this->http->delete("regions/region/$regionId");
        });
    }
    public function uploadFile($filePath, $fileName)
    {
        try {
            $response = $this->http->post('upload', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $fileName
                    ]
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->error('File upload failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
