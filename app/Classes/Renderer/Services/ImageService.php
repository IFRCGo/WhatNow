<?php

namespace App\Classes\Renderer\Services;

use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Exceptions\RcnApiRequestException;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Classes\Renderer\Contracts\ImageClientInterface;
use App\Classes\Renderer\Contracts\ImageFileInterface;
use App\Classes\Renderer\Contracts\ImageInterface;
use App\Classes\Renderer\Entities\EventType;
use App\Classes\Renderer\Entities\Image;
use App\Classes\Renderer\Entities\Language;

class ImageService
{

    protected $client;

    protected $image;


    public function __construct(RcnApiClient $client, ImageClientInterface $image)
    {
        $this->client = $client->whatnow();
        $this->image = $image;
    }

    public function createFromArray($params): Image
    {
        foreach (['instructionId', 'translationCode', 'stageRef'] as $param) {
            if (!isset($params[$param])) {
                throw ImageServiceException::missingParameter($param);
            }
        }

        try {
            if ($params['revision']) {
                $instruction = $this->client->getLatestInstructionRevision($params['instructionId']);
            } else {
                $instruction = $this->client->getInstruction($params['instructionId']);
            }
        } catch (RcnApiRequestException $e) {
            throw ImageServiceException::missingInstruction($params['instructionId']);
        }

                $translations = $instruction->getTranslationsByLanguage($params['translationCode']);

        if (!($translations instanceof InstructionTranslation)) {
            throw ImageServiceException::missingTranslations($params['translationCode']);
        }

                $items = $translations->getStages()->get($params['stageRef']);

        if (gettype($items) !== 'array') {
                        throw ImageServiceException::invalidStageReference($params['stageRef']);
        }

        if (count($items) === 0) {
                        throw ImageServiceException::missingStages($params['stageRef']);
        }

        $societyName = $instruction->getAttribution()->getName();
        $title = $translations->getTitle() ?? '';

        return new Image(
            $societyName,
            $title,
            $params['stageRef'],
            new Language($params['translationCode']),
            new EventType($instruction->getEventType()),
            $items
        );
    }


    public function render(ImageInterface $image, ImageFileInterface $file): string
    {
        $markup = $image->getMarkup();

        if ($file->exists()) {
            $file->delete();
        }

        return $this->image->generate($markup, $file->getPath());
    }
}
