<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;

class TranslationService
{
    protected $client;
    protected $maxRetries = 3;
    protected $batchSize = 30;
    protected $delayBetweenRequests = 250000; // 0.25 segundos
    protected $apiKey;
    protected $applicationId;
    protected $baseUrl;
    protected $useTranslationClient = true;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30.0,
        ]);

        // Configurar API desde variables de entorno
        $this->apiKey = env('IFRC_TRANSLATION_API_KEY', '');
        $this->applicationId = env('IFRC_TRANSLATION_APP_ID', '12');
        $this->baseUrl = env('IFRC_TRANSLATION_API_URL', 'https://ifrcumicroservicesapim001.azure-api.net/translation/api');
    }

    /**
     * Traduce un archivo de idioma a múltiples idiomas de destino
     *
     * @param string $sourceFile Ruta al archivo de idioma fuente
     * @param array $targetLanguages Idiomas de destino
     * @return array Rutas a los archivos traducidos
     */
    public function translateMultipleLanguages($sourceFile, $targetLanguages)
    {
        // Leer el archivo de origen
        $sourceTranslations = json_decode(File::get($sourceFile), true);
        $sourceLanguage = pathinfo($sourceFile, PATHINFO_FILENAME);


        // Resultado final
        $results = [];

        // Procesar cada idioma objetivo
        foreach ($targetLanguages as $targetLanguage) {
            Log::info("Traduciendo del idioma {$sourceLanguage} al idioma {$targetLanguage}");

            $translatedContent = $this->translateJsonContent(
                $sourceTranslations,
                $sourceLanguage,
                $targetLanguage
            );

            // Guardar archivo de traducción
            $outputFile = resource_path("lang/{$targetLanguage}.json");
            File::put($outputFile, json_encode($translatedContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $results[$targetLanguage] = $outputFile;
        }

        return $results;
    }

    /**
     * Traduce el contenido JSON completo
     */
    protected function translateJsonContent($sourceTranslations, $sourceLanguage, $targetLanguage)
    {
        $translatedContent = [];
        $keys = array_keys($sourceTranslations);

        // Dividir en lotes
        $chunks = array_chunk($keys, $this->batchSize);

        foreach ($chunks as $chunkIndex => $chunkKeys) {
            Log::info("Procesando lote " . ($chunkIndex + 1) . " de " . count($chunks));

            foreach ($chunkKeys as $key) {
                $value = $sourceTranslations[$key];

                // Manejar traducciones de objetos anidados
                if (is_array($value)) {
                    $translatedContent[$key] = $this->translateNestedObject($value, $sourceLanguage, $targetLanguage, $key);
                } else {
                    $translatedContent[$key] = $this->translateWithRetry($value, $sourceLanguage, $targetLanguage, $key);
                }

                // Pequeña pausa entre traducciones
                usleep($this->delayBetweenRequests);
            }

            // Pausa más larga entre lotes
            sleep(1);
        }

        return $translatedContent;
    }

    /**
     * Traduce un objeto anidado
     */
    protected function translateNestedObject($object, $sourceLanguage, $targetLanguage, $parentKey = '')
    {
        $translatedObject = [];

        foreach ($object as $nestedKey => $nestedValue) {
            $currentPath = $parentKey ? "$parentKey.$nestedKey" : $nestedKey;

            if (is_array($nestedValue)) {
                // Recursividad para objetos anidados
                $translatedObject[$nestedKey] = $this->translateNestedObject(
                    $nestedValue,
                    $sourceLanguage,
                    $targetLanguage,
                    $currentPath
                );
            } else {
                $translatedObject[$nestedKey] = $this->translateWithRetry(
                    $nestedValue,
                    $sourceLanguage,
                    $targetLanguage,
                    $currentPath
                );
            }
        }

        return $translatedObject;
    }

    /**
     * Traduce un texto con reintentos en caso de fallo
     */
    protected function translateWithRetry($text, $sourceLanguage, $targetLanguage, $key = '')
    {
        // Ignorar traducciones vacías o numéricas
        if (empty($text) || is_numeric($text)) {
            return $text;
        }

        // Procesamiento opcional de claves para soporte de espacios de nombres
        $page = null;
        $translationKey = $key;

        // Si se usan espacios de nombres con el carácter ¤ como se menciona en los requisitos
        if (strpos($key, '¤') !== false) {
            $parts = explode('¤', $key);
            $page = $parts[0];
            $translationKey = $parts[1] ?? $key;
        }

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                if ($this->useTranslationClient) {
                    // Usar la API del Translation Client
                    return $this->translateUsingClient($text, $sourceLanguage, $targetLanguage, $page, $translationKey);
                } else {
                    // Usar la API de traducción directa
                    return $this->translateUsingDirectApi($text, $sourceLanguage, $targetLanguage);
                }
            } catch (Exception $e) {
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

    /**
     * Usa la API del Translation Client (traducciones preparadas)
     */
    public function translateUsingClient($text, $fromLang, $toLang, $page = null, $key = null)
    {
        $queryParams = [
            'KeyPage' => $page,
            'Key' => $key
        ];

        // Eliminar valores nulos
        $queryParams = array_filter($queryParams, function($value) {
            return $value !== null;
        });

        $endpoint = "{$this->baseUrl}/Application/{$this->applicationId}/Translation";

        $response = $this->client->request('GET', $endpoint, [
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'query' => $queryParams
        ]);

        $data = json_decode($response->getBody(), true);

        // Procesar la respuesta según la estructura de la API
        if (isset($data['translations'][$toLang])) {
            return $data['translations'][$toLang];
        }

        // Si no se encuentra en las traducciones preparadas, recurrir a la API directa
        return $this->translateUsingDirectApi($text, $fromLang, $toLang);
    }

    /**
     * Usa la API de traducción directa
     */
    public function translateUsingDirectApi($text, $fromLang, $toLang)
    {
        $endpoint = "{$this->baseUrl}/translate";

        $response = $this->client->request('POST', $endpoint, [
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'text' => $text,
                'from' => $fromLang,
                'to' => $toLang
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Extraer texto traducido de la respuesta
        return $data['translation'] ?? $text;
    }

    /**
     * Configura si usar Translation Client o API directa
     */
    public function setUseTranslationClient($useClient)
    {
        $this->useTranslationClient = $useClient;
        return $this;
    }
}
