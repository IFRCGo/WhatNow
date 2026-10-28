<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Translation',
    description: 'Operations about translation',
)]
class TranslationController extends Controller
{
    
    #[OA\Get(
        path: '/translations/{locale}',
        tags: ['Translation'],
        summary: 'Get translations for a specific locale',
        description: 'Retrieves the translation file for the given locale',
        operationId: 'getTranslations',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'locale',
            in: 'path',
            required: true,
            description: 'Locale code for the translations',
            schema: new OA\Schema(
                type: 'string',
                example: 'en',
            ),
        )],
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
    public function show($locale)
    {
        $path = resource_path("lang/{$locale}.json");

        abort_unless(file_exists($path), 404);

        return (array) json_decode(file_get_contents($path, true));
    }
}
