<?php

namespace Tests\Unit\Renderer;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\Exceptions\RcnApiRequestException;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Classes\Renderer\Contracts\ImageClientInterface;
use App\Classes\Renderer\Contracts\ImageFileInterface;
use App\Classes\Renderer\Contracts\ImageInterface;
use App\Classes\Renderer\Entities\Image;
use App\Classes\Renderer\Services\ImageService;
use App\Classes\Renderer\Services\ImageServiceException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_creates_image()
    {
        $params = $this->getParams();
        $imageValues = $this->getImageValues();

        $stages = \Mockery::mock(Collection::class)
            ->shouldReceive('get')
            ->withArgs([$params['stageRef']])
            ->andReturn($imageValues['items'])
            ->getMock();

        $translations = \Mockery::mock(InstructionTranslation::class)
            ->shouldReceive('getStages')
            ->andReturn($stages)
            ->shouldReceive('getTitle')
            ->andReturn($imageValues['title'])
            ->getMock();

        $attribution = \Mockery::mock(Organisation::class)
            ->shouldReceive('getName')
            ->withNoArgs()
            ->andReturn($imageValues['societyName'])
            ->getMock();

        $instruction = \Mockery::mock(Instruction::class)
            ->shouldReceive('getTranslationsByLanguage')
            ->withArgs([$params['translationCode']])
            ->andReturn($translations)
            ->shouldReceive('getAttribution')
            ->andReturn($attribution)
            ->shouldReceive('getEventType')
            ->andReturn($imageValues['eventTypeName'])
            ->getMock();

        $whatNow = \Mockery::mock(WhatNowResource::class)
            ->shouldReceive('getLatestInstructionRevision')
            ->withArgs([$params['instructionId']])
            ->andReturn($instruction)
            ->getMock();

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $image = $service->createFromArray($params);

        $this->assertEquals(Image::class, get_class($image));

        $this->assertEquals($imageValues['societyName'], $image->getSocietyName());
        $this->assertEquals($imageValues['title'], $image->getTitle());
        $this->assertEquals($imageValues['stageRef'], $image->getStageRef());
        $this->assertEquals($imageValues['langCode'], $image->getLanguage()->getCode());
        $this->assertEquals($imageValues['eventTypeName'], $image->getEventType()->getName());
        $this->assertEquals($imageValues['eventTypeIconName'], $image->getEventType()->getIconName());
        $this->assertEquals($imageValues['items'], $image->getItems());
    }

    /**
     * @dataProvider getMissingParamsWithExceptions
     */
    public function test_it_throws_exception_if_parameter_is_missing($missingParam)
    {
        $params = $this->getParams();
        unset($params[$missingParam]);

        $this->expectExceptionObject(ImageServiceException::missingParameter($missingParam));

        $whatNow = \Mockery::mock(WhatNowResource::class);

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $service->createFromArray($params);
    }

    public function test_it_throws_exception_if_instruction_is_missing()
    {
        $params = $this->getParams();

        $this->expectExceptionObject(ImageServiceException::missingInstruction($params['instructionId']));

        $whatNow = \Mockery::mock(WhatNowResource::class)
            ->shouldReceive('getLatestInstructionRevision')
            ->withArgs([$params['instructionId']])
            ->andThrow(RcnApiRequestException::class)
            ->getMock();

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $service->createFromArray($params);
    }

    public function test_it_throws_exception_if_translations_are_missing()
    {
        $params = $this->getParams();

        $this->expectExceptionObject(ImageServiceException::missingTranslations($params['translationCode']));

        $instruction = \Mockery::mock(Instruction::class)
            ->shouldReceive('getTranslationsByLanguage')
            ->withArgs([$params['translationCode']])
            ->andReturn(null)   // this value is not an InstructionTranslation object, so should trigger the exception
            ->getMock();

        $whatNow = \Mockery::mock(WhatNowResource::class)
            ->shouldReceive('getLatestInstructionRevision')
            ->withArgs([$params['instructionId']])
            ->andReturn($instruction)
            ->getMock();

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $service->createFromArray($params);
    }

    public function test_it_throws_exception_if_stage_reference_is_invalid()
    {
        $params = $this->getParams();

        $this->expectExceptionObject(ImageServiceException::invalidStageReference($params['stageRef']));

        $stages = \Mockery::mock(Collection::class)
            ->shouldReceive('get')
            ->withArgs([$params['stageRef']])
            ->andReturn(null)     // this non-array value should trigger the exception
            ->getMock();

        $translations = \Mockery::mock(InstructionTranslation::class)
            ->shouldReceive('getStages')
            ->andReturn($stages)
            ->getMock();

        $instruction = \Mockery::mock(Instruction::class)
            ->shouldReceive('getTranslationsByLanguage')
            ->withArgs([$params['translationCode']])
            ->andReturn($translations)
            ->getMock();

        $whatNow = \Mockery::mock(WhatNowResource::class)
            ->shouldReceive('getLatestInstructionRevision')
            ->withArgs([$params['instructionId']])
            ->andReturn($instruction)
            ->getMock();

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $service->createFromArray($params);
    }

    public function test_it_throws_exception_if_stages_are_missing()
    {
        $params = $this->getParams();

        $this->expectExceptionObject(ImageServiceException::missingStages($params['stageRef']));

        $stages = \Mockery::mock(Collection::class)
            ->shouldReceive('get')
            ->withArgs([$params['stageRef']])
            ->andReturn([])     // this empty array should trigger the exception
            ->getMock();

        $translations = \Mockery::mock(InstructionTranslation::class)
            ->shouldReceive('getStages')
            ->andReturn($stages)
            ->getMock();

        $instruction = \Mockery::mock(Instruction::class)
            ->shouldReceive('getTranslationsByLanguage')
            ->withArgs([$params['translationCode']])
            ->andReturn($translations)
            ->getMock();

        $whatNow = \Mockery::mock(WhatNowResource::class)
            ->shouldReceive('getLatestInstructionRevision')
            ->withArgs([$params['instructionId']])
            ->andReturn($instruction)
            ->getMock();

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class);

        $service = new ImageService($client, $imageClient);
        $service->createFromArray($params);
    }

    public function test_it_renders_image()
    {
        $whatNow = \Mockery::mock(WhatNowResource::class);

        $client = \Mockery::mock(RcnApiClient::class)
            ->shouldReceive('whatnow')
            ->andReturn($whatNow)
            ->getMock();

        $imageClient = \Mockery::mock(ImageClientInterface::class)
            ->shouldReceive('generate')
            ->withArgs(['image_markup', 'image_path'])
            ->andReturn('image_path')
            ->getMock();

        $image = \Mockery::mock(ImageInterface::class)
            ->shouldReceive('getMarkup')
            ->once()
            ->andReturn('image_markup')
            ->getMock();

        $file = \Mockery::mock(ImageFileInterface::class)
            ->shouldReceive('exists')
            ->once()
            ->andReturn(true)
            ->shouldReceive('delete')
            ->once()
            ->shouldReceive('getPath')
            ->once()
            ->andReturn('image_path')
            ->getMock();

        $service = new ImageService($client, $imageClient);
        $path = $service->render($image, $file);

        $this->assertEquals('image_path', $path);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getMissingParamsWithExceptions()
    {
        return [
            ['instructionId'],
            ['translationCode'],
            ['stageRef']
        ];
    }

    protected function getParams()
    {
        return [
            'instructionId' => 1,
            'translationCode' => 'myTranslationCode',
            'stageRef' => 'myStageRef'
        ];
    }

    protected function getImageValues()
    {
        return [
            'societyName' => 'mySociety',
            'title' => 'myTitle',
            'stageRef' => $this->getParams()['stageRef'],
            'langCode' => $this->getParams()['translationCode'],
            'eventTypeName' => 'myEventTypeName',
            'eventTypeIconName' => 'general@3x.png',
            'items' => ['item1', 'item2', 'item3']
        ];
    }
}
