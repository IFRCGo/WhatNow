<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Services\TranslationService;

/**
 * @OA\Tag(
 *     name="Translation",
 *     description="Operations about translation"
 * )
 */
class TranslationController extends Controller
{
    protected $translationService;

    /**
     * Create a new controller instance.
     *
     * @param TranslationService $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

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

    /**
     * @OA\Post(
     *     path="/translations/generate",
     *     tags={"Translation"},
     *     summary="Generate translations using Artisan command",
     *     description="Generates translations for specified target languages using the source language file",
     *     operationId="generateTranslations",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="sourceLanguage",
     *                 type="string",
     *                 description="Source language code",
     *                 example="en"
     *             ),
     *             @OA\Property(
     *                 property="targetLanguages",
     *                 type="array",
     *                 description="List of target language codes",
     *                 @OA\Items(type="string", example="es")
     *             ),
     *             @OA\Property(
     *                 property="useTranslationClient",
     *                 type="boolean",
     *                 description="Whether to use the Translation Client API (true) or direct API (false)",
     *                 example=true
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful translation generation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Translations generated successfully"
     *             )
     *         )
     *     )
     * )
     */
    public function generate(Request $request)
    {
        $request->validate([
            'sourceLanguage' => 'string|min:2|max:5',
            'targetLanguages' => 'required|array',
            'targetLanguages.*' => 'string|min:2|max:5',
            'useTranslationClient' => 'boolean'
        ]);

        $sourceLanguage = $request->input('sourceLanguage', 'en');
        $targetLanguages = $request->input('targetLanguages');
        $useClient = $request->input('useTranslationClient', true);

        // Construir comando Artisan
        $command = "translatte:generate --source={$sourceLanguage}";

        foreach ($targetLanguages as $lang) {
            $command .= " --target={$lang}";
        }

        if ($useClient) {
            $command .= " --client";
        }

        // Ejecutar comando
        try {
            $exitCode = Artisan::call($command);

            if ($exitCode !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating translations: ' . Artisan::output()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Translations generated successfully',
                'details' => trim(Artisan::output())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/translations/available",
     *     tags={"Translation"},
     *     summary="Get available translation locales",
     *     description="Returns a list of all available translation locales",
     *     operationId="getAvailableTranslations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="string", example="en")
     *             )
     *         )
     *     )
     * )
     */
    public function available()
    {
        $langPath = resource_path('lang');
        $files = File::files($langPath);

        $locales = [];
        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $locales[] = $file->getFilenameWithoutExtension();
            }
        }

        return response()->json([
            'data' => $locales
        ]);
    }

    /**
     * @OA\Post(
     *     path="/translations/translate",
     *     tags={"Translation"},
     *     summary="Translate a single text",
     *     description="Translates a single text from one language to another",
     *     operationId="translateText",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"text", "to"},
     *             @OA\Property(property="text", type="string", example="Hello world"),
     *             @OA\Property(property="from", type="string", example="en", description="Source language code"),
     *             @OA\Property(property="to", type="string", example="es", description="Target language code"),
     *             @OA\Property(property="page", type="string", example="common", description="Optional page/namespace for Translation Client"),
     *             @OA\Property(property="key", type="string", example="greeting", description="Optional key for Translation Client"),
     *             @OA\Property(property="useTranslationClient", type="boolean", example=true)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="original", type="string", example="Hello world"),
     *             @OA\Property(property="translated", type="string", example="Hola mundo")
     *         )
     *     )
     * )
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'from' => 'required|string|min:2|max:5',
            'to' => 'required|string|min:2|max:5',
            'page' => 'nullable|string',
            'key' => 'nullable|string',
            'useTranslationClient' => 'boolean'
        ]);

        $text = $request->input('text');
        $fromLang = $request->input('from');
        $toLang = $request->input('to');
        $page = $request->input('page');
        $key = $request->input('key');
        $useClient = $request->input('useTranslationClient', true);

        // Establecer qué API usar
        $this->translationService->setUseTranslationClient($useClient);

        try {
            if ($useClient) {
                $translatedText = $this->translationService->translateUsingClient($text, $fromLang, $toLang, $page, $key);
            } else {
                $translatedText = $this->translationService->translateUsingDirectApi($text, $fromLang, $toLang);
            }

            return response()->json([
                'original' => $text,
                'translated' => $translatedText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Translation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
