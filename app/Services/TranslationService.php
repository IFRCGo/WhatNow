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
    protected $delayBetweenRequests = 250000; // 0.25 seconds
    protected $apiKey;
    protected $applicationId;
    protected $baseUrl;
    protected $useTranslationClient = true;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30.0,
        ]);

        // Configure API from environment variables
        $this->apiKey = env('IFRC_TRANSLATION_API_KEY', '');
        $this->applicationId = env('IFRC_TRANSLATION_APP_ID', '12');
        $this->baseUrl = rtrim(env('IFRC_TRANSLATION_API_URL', 'https://ifrc-translationapi-staging.azurewebsites.net/api'), '/');
    }

    /**
     * Translate a language file to multiple target languages
     *
     * @param string $sourceFile Path to the source language file
     * @param array $targetLanguages Target languages
     * @return array Paths to the translated files
     */
    public function translateMultipleLanguages($sourceFile, $targetLanguages)
    {
        // Read the source file
        $sourceTranslations = json_decode(File::get($sourceFile), true);
        $sourceLanguage = pathinfo($sourceFile, PATHINFO_FILENAME);

        // Final result
        $results = [];

        // Process each target language
        foreach ($targetLanguages as $targetLanguage) {
            Log::info("Translating from language {$sourceLanguage} to language {$targetLanguage}");

            $translatedContent = $this->translateJsonContent(
                $sourceTranslations,
                $sourceLanguage,
                $targetLanguage
            );

            // Save translation file
            $outputFile = resource_path("lang/{$targetLanguage}.json");
            File::put($outputFile, json_encode($translatedContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $results[$targetLanguage] = $outputFile;
        }

        return $results;
    }

    /**
     * Translate the complete JSON content
     */
    protected function translateJsonContent($sourceTranslations, $sourceLanguage, $targetLanguage)
    {
        $translatedContent = [];
        $keys = array_keys($sourceTranslations);

        // Split into batches
        $chunks = array_chunk($keys, $this->batchSize);

        foreach ($chunks as $chunkIndex => $chunkKeys) {
            Log::info("Processing batch " . ($chunkIndex + 1) . " of " . count($chunks));

            foreach ($chunkKeys as $key) {
                $value = $sourceTranslations[$key];

                // Handle nested object translations
                if (is_array($value)) {
                    $translatedContent[$key] = $this->translateNestedObject($value, $sourceLanguage, $targetLanguage, $key);
                } else {
                    $translatedContent[$key] = $this->translateWithRetry($value, $sourceLanguage, $targetLanguage, $key);
                }

                // Small pause between translations
                usleep($this->delayBetweenRequests);
            }

            // Longer pause between batches
            sleep(1);
        }

        return $translatedContent;
    }

    /**
     * Translate a nested object
     */
    protected function translateNestedObject($object, $sourceLanguage, $targetLanguage, $parentKey = '')
    {
        $translatedObject = [];

        foreach ($object as $nestedKey => $nestedValue) {
            $currentPath = $parentKey ? "$parentKey.$nestedKey" : $nestedKey;

            if (is_array($nestedValue)) {
                // Recursion for nested objects
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
     * Translate text with retries in case of failure
     */
    protected function translateWithRetry($text, $sourceLanguage, $targetLanguage, $key = '')
    {
        // Ignore empty or numeric translations
        if (empty($text) || is_numeric($text)) {
            return $text;
        }

        // Optional key processing for namespace support
        $page = null;
        $translationKey = $key;

        // If namespaces are used with the character ¤
        if (strpos($key, '¤') !== false) {
            $parts = explode('¤', $key);
            $page = $parts[0];
            $translationKey = $parts[1] ?? $key;
        }

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                if ($this->useTranslationClient) {
                    // Use Translation Client API
                    return $this->translateUsingClient($text, $sourceLanguage, $targetLanguage, $page, $translationKey);
                } else {
                    // Use direct translation API
                    return $this->translateUsingDirectApi($text, $sourceLanguage, $targetLanguage);
                }
            } catch (Exception $e) {
                Log::warning("Translation attempt failed (Attempt {$attempt}): {$e->getMessage()}");

                if ($attempt == $this->maxRetries) {
                    Log::error("Translation failed for: {$text}");
                    return $text; // Return original text if all attempts fail
                }

                // Wait before retrying
                sleep($attempt * 2);
            }
        }

        return $text;
    }

    /**
     * Use Translation Client API (prepared translations)
     */
    public function translateUsingClient($text, $fromLang, $toLang, $page = null, $key = null)
    {
        $queryParams = [
            'KeyPage' => $page,
            'Key' => $key
        ];

        // Remove null values
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

        // Process response according to API structure
        if (isset($data['translations'][$toLang])) {
            return $data['translations'][$toLang];
        }

        // If not found in prepared translations, fall back to direct API
        return $this->translateUsingDirectApi($text, $fromLang, $toLang);
    }

    /**
     * Use direct translation API
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

        // Extract translated text from response
        return $data[0]['translations'][0]['text'] ?? $text;
    }

    /**
     * Configure whether to use Translation Client or direct API
     */
    public function setUseTranslationClient($useClient)
    {
        $this->useTranslationClient = $useClient;
        return $this;
    }
}
