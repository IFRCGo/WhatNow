<?php

namespace App\Http\Controllers;

/**
 * @OA\Tag(
 *     name="Translation",
 *     description="Operations about translation"
 * )
 */
class TranslationController extends Controller
{
    
    /**
     * @OA\Get(
     *     path="/translations/{locale}",
     *     tags={"Translation"},
     *     summary="Get translations for a specific locale",
     *     description="Retrieves the translation file for the given locale",
     *     operationId="getTranslations",
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="Locale code for the translations",
     *         @OA\Schema(type="string", example="en")
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
    public function show($locale)
    {
        $path = resource_path("lang/{$locale}.json");

        abort_unless(file_exists($path), 404);

        return (array) json_decode(file_get_contents($path, true));
    }
}
