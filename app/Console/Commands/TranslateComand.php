<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TranslationService;

class MultiLanguageTranslationCommand extends Command
{
    protected $signature = 'translate:all
                            {--source=en : Idioma de origen}
                            {--languages=* : Lista de códigos de idioma a traducir}';

    protected $description = 'Traduce archivos JSON a múltiples idiomas';

    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
    }

    public function handle()
    {
        $sourceLanguage = $this->option('source');
        $targetLanguages = $this->option('languages');

        // Validar idiomas
        if (empty($targetLanguages)) {
            $this->error('Debes especificar al menos un idioma de destino.');
            return 1;
        }

        // Ruta del archivo de origen
        $sourceFile = resource_path("lang/{$sourceLanguage}.json");

        if (!file_exists($sourceFile)) {
            $this->error("El archivo de origen {$sourceFile} no existe.");
            return 1;
        }

        // Barra de progreso
        $this->info('Iniciando traducción...');
        $progressBar = $this->output->createProgressBar(count($targetLanguages));
        $progressBar->start();

        // Traducir
        $results = $this->translationService->translateMultipleLanguages(
            $sourceFile,
            $targetLanguages
        );

        // Finalizar progreso
        $progressBar->finish();

        // Mostrar resultados
        $this->info("\n\nTraducción completada:");
        foreach ($results as $language => $file) {
            $this->line("- {$language}: {$file}");
        }

        return 0;
    }
}
