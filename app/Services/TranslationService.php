<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TranslationService
{
    protected $client;
    protected $maxRetries = 3;
    protected $batchSize = 30;
    protected $delayBetweenRequests = 250000; // 0.25 segundos

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30.0,
        ]);
    }

    public function translateMultipleLanguages($sourceFile, $targetLanguages)
    {
        // Leer el archivo de origen
        $sourceTranslations = json_decode(File::get($sourceFile), true);

        // Resultado final
        $results = [];

        // Procesar cada idioma objetivo
        foreach ($targetLanguages as $targetLanguage) {
            Log::info("Traduciendo al idioma: {$targetLanguage}");

            $translatedContent = $this->translateJsonContent(
                $sourceTranslations,
                $targetLanguage
            );

            // Guardar archivo de traducción
            $outputFile = resource_path("lang/{$targetLanguage}.json");
            File::put($outputFile, json_encode($translatedContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $results[$targetLanguage] = $outputFile;
        }

        return $results;
    }

    protected function translateJsonContent($sourceTranslations, $targetLanguage)
    {
        $translatedContent = [];
        $keys = array_keys($sourceTranslations);
        $totalKeys = count($keys);

        // Dividir en lotes
        $chunks = array_chunk($keys, $this->batchSize);

        foreach ($chunks as $chunkIndex => $chunkKeys) {
            Log::info("Procesando lote " . ($chunkIndex + 1) . " de " . count($chunks));

            foreach ($chunkKeys as $key) {
                $value = $sourceTranslations[$key];

                // Manejar traducciones de objetos anidados
                if (is_array($value)) {
                    $translatedContent[$key] = $this->translateNestedObject($value, $targetLanguage);
                } else {
                    $translatedContent[$key] = $this->translateWithRetry($value, $targetLanguage);
                }

                // Pequeña pausa entre traducciones
                usleep($this->delayBetweenRequests);
            }

            // Pausa más larga entre lotes
            sleep(1);
        }

        return $translatedContent;
    }

    protected function translateNestedObject($object, $targetLanguage)
    {
        $translatedObject = [];

        foreach ($object as $nestedKey => $nestedValue) {
            if (is_array($nestedValue)) {
                // Recursividad para objetos anidados
                $translatedObject[$nestedKey] = $this->translateNestedObject($nestedValue, $targetLanguage);
            } else {
                $translatedObject[$nestedKey] = $this->translateWithRetry($nestedValue, $targetLanguage);
            }
        }

        return $translatedObject;
    }

    protected function translateWithRetry($text, $targetLanguage)
    {
        // Ignorar traducciones vacías o numéricas
        if (empty($text) || is_numeric($text)) {
            return $text;
        }

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = $this->client->request('GET', 'https://translate.googleapis.com/translate_a/single', [
                    'query' => [
                        'client' => 'gtx',
                        'sl' => 'en',
                        'tl' => $targetLanguage,
                        'dt' => 't',
                        'q' => $text
                    ]
                ]);

                $translationData = json_decode($response->getBody(), true);
                $translatedText = $translationData[0][0][0] ?? $text;

                return $translatedText;
            } catch (\Exception $e) {
                Log::warning("Intento de traducción fallido (Intento {$attempt}): {$e->getMessage()}");

                if ($attempt == $this->maxRetries) {
                    Log::error("Traducción fallida para: {$text}");
                    return $text; // Devolver texto original si todos los intentos fallan
                }

                // Esperar antes de reintentar
                sleep($attempt * 2);
            }
        }

        return $text;
    }
}
