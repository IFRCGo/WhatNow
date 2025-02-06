<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceConflictException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Classes\Renderer\Entities\ImageFile;
use App\Classes\Renderer\Services\ImageService;
use App\Classes\Renderer\Services\ImageServiceException;
use App\Events\WhatNow\InstructionCreated;
use App\Events\WhatNow\InstructionDeleted;
use App\Events\WhatNow\InstructionTranslationCreated;
use App\Events\WhatNow\InstructionTranslationPublished;
use App\Events\WhatNow\InstructionTranslationUnpublished;
use App\Events\WhatNow\InstructionUpdated;
use App\Http\Controllers\ApiController;
use App\Http\Resources\WhatNow\InstructionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

final class InstructionController extends ApiController
{

    private $client;


    private $image;


    public function __construct(RcnApiClient $client, ImageService $image)
    {
        $this->client = $client->whatnow();
        $this->image = $image;
    }


    public function listByCountryCode(string $countryCode)
    {
        try {
            $instructions = $this->client->getPublishedInstructionsByCountryCode($countryCode);

            $resource = InstructionResource::collection($instructions);

                                    if ($instructions->isNotEmpty()) {
                $availableLanguages = new Collection(array_keys($instructions->first()->getAttribution()->getTranslations()));
            } else {
                $availableLanguages = new Collection();
            }

            $instructions->each(function (Instruction $instruction) use ($availableLanguages) {
                foreach ($instruction->getAvailableLanguages() as $language) {
                    $availableLanguages->push($language);
                }
            });

            $resource->additional([
                'meta' => [
                    'availableLanguages' => $availableLanguages->unique()->toArray(),
                ],
            ]);

            return $resource;
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function exportCsv()
    {
        $this->authorize('export', Instruction::class);

        try {
            $instructions = $this->client->getAllInstructions();
            $now = now();
            $fileName = 'WhatNow_message_report_'.$now->format('Y-m-d').'_'.$now->format('h-i').'.csv';
            $rows = [];
            $columnNames = ['Alert ID', 'Language', 'Urgency', 'HZ type', 'National society', 'Published date'];

            $instructions->each(function (Instruction $instruction) use (&$rows) {
                foreach ($instruction->getTranslations() as $translation) {

                    foreach ($translation->getStages() as $stage => $stageContent) {
                        $rows[] = [
                            $instruction->getId(),
                            $translation->getLang(),
                            $translation->getUrgencyId(),
                            $instruction->getEventType(),
                            $instruction->getAttribution()->getName(),
                            $translation->getPublished(),
                        ];
                    }
                }
            });

            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];
            $callback = function () use ($columnNames, $rows) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columnNames);
                foreach ($rows as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function listLatestDraftsByCountryCode(string $countryCode)
    {
        $this->authorize('listDrafts', Instruction::class);

        try {
            $instructions = $this->client->getLatestInstructionsByCountryCode($countryCode);

            $resource = InstructionResource::collection($instructions);


            $availableLanguages = new Collection();
            $instructions->each(function (Instruction $instruction) use ($availableLanguages) {
                foreach ($instruction->getAvailableLanguages() as $language) {
                    $availableLanguages->push($language);
                }
            });

            $resource->additional([
                'meta' => [
                    'availableLanguages' => $availableLanguages->unique()->values()->toArray(),
                ],
            ]);

            return $resource;
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function view(int $id)
    {
        try {
            $instruction = $this->client->getInstruction($id);

            $resource = InstructionResource::make($instruction);
            $resource->additional([
                'availableLanguages' => $instruction->getAvailableLanguages(),
            ]);

            return $resource;
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function viewLatestDraft(int $id)
    {
        $this->authorize('viewDrafts', Instruction::class);

        try {
            $instruction = $this->client->getLatestInstructionRevision($id);

            $resource = InstructionResource::make($instruction);
            $resource->additional([
                'availableLanguages' => $instruction->getAvailableLanguages(),
            ]);

            return $resource;
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'countryCode' => 'required|alpha|size:3',
            'eventType' => 'required|string|max:50',
            'translations' => 'required|array',
            'translations.*.lang' => 'required|alpha|size:2',
            'translations.*.title' => 'required|string',
            'translations.*.description' => 'required|string',
            'translations.*.webUrl' => 'nullable|url',
            'translations.*.urgency_id' => 'required|integer|exists:urgencies,id',
        ]);

        $data = $request->all();

        $instruction = Instruction::createFromRequest($data);

        try {
            $instruction = $this->client->createInstruction($instruction);
            event(new InstructionCreated($instruction));

            return new JsonResponse(InstructionResource::make($instruction), JsonResponse::HTTP_CREATED);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiResourceConflictException $e) {
            return $this->respondWithConflict($e, trans('rcnapi.conflict.instruction'));
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'countryCode' => 'required|alpha|size:3',
            'eventType' => 'required|string|max:50',
            'translations' => 'required|array',
            'translations.*.lang' => 'required|alpha|size:2',
            'translations.*.title' => 'required|string',
            'translations.*.description' => 'required|string',
            'translations.*.webUrl' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['id'] = $id;

        $instruction = Instruction::createFromRequest($data);

        try {
            $instruction = $this->client->updateInstruction($instruction);

            event(new InstructionUpdated($instruction));

            return InstructionResource::make($instruction);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    public function delete(int $id)
    {
        try {
            $instruction = $this->client->getLatestInstructionRevision($id);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }

        $this->authorize('delete', $instruction);

        try {
            $this->client->deleteInstruction($id);
            event(new InstructionDeleted($instruction));

            return $this->respondWithSuccess();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function createTranslation(Request $request, int $id)
    {
        try {
            $instruction = $this->client->getInstruction($id);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }

        $this->authorize('publish', $instruction);

        $this->validate($request, [
            'lang' => 'required|alpha|size:2',
            'title' => 'required|string',
            'description' => 'required|string',
            'stages' => 'array',
        ]);

        $translation = InstructionTranslation::createFromRequest($request->all());

        try {
            $this->client->createTranslation($id, $translation);
            event(new InstructionTranslationCreated($instruction, $translation));

            return $this->respondWithSuccess();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function publishTranslation(Request $request, int $id, int $translationId)
    {
        try {
                                    $instruction = $this->client->getLatestInstructionRevision($id);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }

        $lang = null;

        foreach ($instruction->getTranslations() as $translation) {
            if ($translationId === $translation->getId()) {
                $lang = $translation->getLang();
            }
        }

        if (is_null($lang)) {
            return new JsonResponse(['message' => 'Translation does not exist'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->authorize('publish', $instruction);

        $this->validate($request, [
            'published' => 'required|bool',
        ]);

        $patch = $request->only('published');

        try {
            $this->client->patchTranslation($id, $translationId, $patch);

            if ($patch) {
                event(new InstructionTranslationPublished($instruction, $lang));
            } else {
                event(new InstructionTranslationUnpublished($instruction, $lang));
            }

            return $this->respondWithSuccess();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function publishTranslations($countryCode, Request $request)
    {
        $this->authorize('publishMultiple', Instruction::class);

        $this->validate($request, [
            'translations' => 'required|array',
            'translations.*.id' => 'required|int',
            'translations.*.lang' => 'required|alpha|size:2',
            'translations.*.eventType' => 'required|string',
        ]);

        $translations = $request->only('translations')['translations'];

        try {
            $translationIds = array_map(function (array $translation) {
                return $translation['id'];
            }, $translations);

            $this->client->publishTranslations($translationIds);

            foreach ($translations as $translation) {
                $instruction = Instruction::createFromRequest([
                    'countryCode' => $countryCode,
                    'eventType' => $translation['eventType'],
                    'translations' => [],
                ]);
                event(new InstructionTranslationPublished($instruction, $translation['lang']));
            }

            return $this->respondWithSuccess();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function renderImage(Request $request, $instructionId, $translationCode, $stageRef)
    {
        try {
            $revision = $request->query('revision') == 'true';
            $image = $this->image->createFromArray([
                'instructionId' => $instructionId,
                'translationCode' => $translationCode,
                'stageRef' => $stageRef,
                'revision' => $revision
            ]);
        } catch (ImageServiceException $e) {
            return $this->respondWithNotFound($e, $e->getMessage());
        }

        $filename = $instructionId . '_' . $translationCode . '_' . $stageRef . '.jpg';
        $imageFile = new ImageFile(config('whatnowimage.directory'), $filename);
        $path = $this->image->render($image, $imageFile);

        return response()->file($path);
    }
}
