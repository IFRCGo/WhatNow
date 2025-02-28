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
/**
 * @OA\Tag(
 *     name="Instructions",
 *     description="Operations about Messages/Supporting Messages/Translations"
 * )
 */
final class InstructionController extends ApiController
{

    private $client;


    private $image;


    public function __construct(RcnApiClient $client, ImageService $image)
    {
        $this->client = $client->whatnow();
        $this->image = $image;
    }


    /**
     * @OA\Get(
     *     path="/organisations/{countryCode}/instructions",
     *     tags={"Instructions"},
     *     summary="Get instructions by country code",
     *     description="Returns a list of published instructions filtered by the specified country code",
     *     operationId="listInstructionsByCountryCode",
     *     @OA\Parameter(
     *         name="countryCode",
     *         in="path",
     *         description="The country code to filter instructions by",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="availableLanguages",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/organisations/instructions/export",
     *     tags={"Instructions"},
     *     summary="Export instructions as CSV",
     *     description="Exports a CSV file containing instruction data",
     *     operationId="exportCsv",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/organisations/{countryCode}/instructions/revisions/latest",
     *     tags={"Instructions"},
     *     summary="List latest draft instructions by country code",
     *     description="Retrieves the latest draft instructions for a given country code",
     *     operationId="listLatestDraftsByCountryCode",
     *     @OA\Parameter(
     *         name="countryCode",
     *         in="path",
     *         required=true,
     *         description="Country code to filter the latest draft instructions",
     *         @OA\Schema(type="string")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/instructions/{id}",
     *     tags={"Instructions"},
     *     summary="Get instruction by ID",
     *     description="Retrieves a specific instruction by its ID",
     *     operationId="viewInstruction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/instructions/{id}/revisions/latest",
     *     tags={"Instructions"},
     *     summary="Get latest draft revision of an instruction",
     *     description="Retrieves the latest draft revision of a specific instruction by its ID",
     *     operationId="viewLatestDraft",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction to retrieve the latest draft revision",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Post(
     *     path="/instructions",
     *     tags={"Instructions"},
     *     summary="Create a new instruction",
     *     description="Creates a new instruction with translations",
     *     operationId="createInstruction",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"countryCode", "eventType", "translations"},
     *             @OA\Property(property="countryCode", type="string", description="Country code (3-letter ISO format)", example="USA"),
     *             @OA\Property(property="eventType", type="string", maxLength=50, description="Type of event", example="conference"),
     *             @OA\Property(
     *                 property="translations",
     *                 type="array",
     *                 description="Array of translations",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"lang", "title", "description"},
     *                     @OA\Property(property="lang", type="string", description="Language code (2-letter ISO format)", example="en"),
     *                     @OA\Property(property="title", type="string", description="Title of the instruction", example="Safety Guidelines"),
     *                     @OA\Property(property="description", type="string", description="Description of the instruction", example="Follow these steps to ensure safety."),
     *                     @OA\Property(property="webUrl", type="string", format="url", nullable=true, description="Optional URL for additional information", example="https://example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Put(
     *     path="/instructions/{id}",
     *     tags={"Instructions"},
     *     summary="Update an existing instruction",
     *     description="Updates an instruction with new data",
     *     operationId="updateInstruction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"countryCode", "eventType", "translations"},
     *             @OA\Property(property="countryCode", type="string", description="Country code (3-letter ISO format)", example="USA"),
     *             @OA\Property(property="eventType", type="string", maxLength=50, description="Type of event", example="conference"),
     *             @OA\Property(
     *                 property="translations",
     *                 type="array",
     *                 description="Array of translations",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"lang", "title", "description"},
     *                     @OA\Property(property="lang", type="string", description="Language code (2-letter ISO format)", example="en"),
     *                     @OA\Property(property="title", type="string", description="Title of the instruction", example="Safety Guidelines"),
     *                     @OA\Property(property="description", type="string", description="Description of the instruction", example="Follow these steps to ensure safety."),
     *                     @OA\Property(property="webUrl", type="string", format="url", nullable=true, description="Optional URL for additional information", example="https://example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Delete(
     *     path="/instructions/{id}",
     *     tags={"Instructions"},
     *     summary="Delete an instruction",
     *     description="Deletes an instruction by its ID",
     *     operationId="deleteInstruction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/instructions/{id}/revisions/",
     *     tags={"Instructions"},
     *     summary="Create a translation for an instruction",
     *     description="Creates a new translation for a specific instruction by its ID",
     *     operationId="createTranslation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"lang", "title", "description"},
     *             @OA\Property(property="lang", type="string", description="Language code (ISO 639-1, 2 characters)"),
     *             @OA\Property(property="title", type="string", description="Title of the instruction"),
     *             @OA\Property(property="description", type="string", description="Description of the instruction"),
     *             @OA\Property(property="stages", type="array", @OA\Items(type="string"), description="Array of stages")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/instructions/{id}/revisions/{translationId}",
     *     tags={"Instructions"},
     *     summary="Publish or unpublish a translation",
     *     description="Updates the publication status of a specific translation for an instruction",
     *     operationId="publishTranslation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the instruction",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="translationId",
     *         in="path",
     *         required=true,
     *         description="ID of the translation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"published"},
     *             @OA\Property(property="published", type="boolean", description="Publication status (true for published, false for unpublished)")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/instructions/{countryCode}/publish",
     *     tags={"Instructions"},
     *     summary="Publish translations for a given country",
     *     description="Publishes instruction translations for a specific country code",
     *     operationId="publishTranslations",
     *     @OA\Parameter(
     *         name="countryCode",
     *         in="path",
     *         required=true,
     *         description="Country code for which translations will be published",
     *         @OA\Schema(type="string", example="USA")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"translations"},
     *             @OA\Property(
     *                 property="translations",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id", "lang", "eventType"},
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="lang", type="string", example="en"),
     *                     @OA\Property(property="eventType", type="string", example="concert")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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
