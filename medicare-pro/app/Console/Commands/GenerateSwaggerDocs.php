<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSwaggerDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Swagger/OpenAPI documentation';

    /**
     * Execute the console command.
     *
     * Executes the L5-Swagger `l5-swagger:generate` Artisan command
     * to scan controller annotations and produce the OpenAPI JSON
     * and YAML files that power the API documentation UI.
     */
    public function handle(): int
    {
        $this->info('Generating Swagger/OpenAPI documentation...');

        try {
            $exitCode = $this->call('l5-swagger:generate');

            if ($exitCode === self::SUCCESS) {
                $this->info('Swagger documentation generated successfully.');
                Log::info('GenerateSwaggerDocs command: Documentation generated successfully.');

                return self::SUCCESS;
            }

            $this->error('l5-swagger:generate returned a non-zero exit code.');
            Log::warning('GenerateSwaggerDocs command: l5-swagger:generate returned exit code.', [
                'exit_code' => $exitCode,
            ]);

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error("Failed to generate Swagger documentation: {$e->getMessage()}");
            Log::error('GenerateSwaggerDocs command: Failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
