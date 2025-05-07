<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslateLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translatte:generate
                            {--source=en : Source language code}
                            {--target=* : Target language codes (multiple allowed)}
                            {--client : Use Translation Client API instead of direct translation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate translations for specified languages using IFRC Translation API';

    /**
     * The translation service instance.
     *
     * @var \App\Services\TranslationService
     */
    protected $translationService;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\TranslationService  $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sourceLanguage = $this->option('source');
        $targetLanguages = $this->option('target');
        $useClient = $this->option('client') ?? false; // Default to false for direct API

        // Validate source language file exists
        $sourceFile = resource_path("lang/{$sourceLanguage}.json");
        if (!File::exists($sourceFile)) {
            $this->error("Source language file not found: {$sourceFile}");
            return 1;
        }

        // Validate that we have at least one target language
        if (empty($targetLanguages)) {
            $this->error('At least one target language must be specified using --target option');
            return 1;
        }

        $this->info('Starting translation process');
        $this->info("Source language: {$sourceLanguage}");
        $this->info("Target languages: " . implode(', ', $targetLanguages));
        $this->info("Using " . ($useClient ? "Translation Client API" : "Direct Translation API"));

        try {
            // Configure translation service
            $this->translationService->setUseTranslationClient($useClient);

            // Start the translation process
            $this->info('Translating language files...');
            $results = $this->translationService->translateMultipleLanguages($sourceFile, $targetLanguages);

            // Display results
            $this->info('Translation completed successfully!');
            $this->info('Generated translation files:');

            foreach ($results as $language => $filePath) {
                $this->info("- {$language}: {$filePath}");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Translation process failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
