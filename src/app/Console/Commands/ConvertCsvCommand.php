<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Schema\CsvToYamlConverter;
use Illuminate\Console\Command;

class ConvertCsvCommand extends Command
{
    protected $signature = 'schema:csv
                            {name : Table name (folder name under csv/)}
                            {--alter : Convert as alter table CSV}';

    protected $description = 'Convert CSV schema definition to YAML format';

    public function handle(): int
    {
        $name = $this->argument('name');
        $isAlter = (bool) $this->option('alter');

        $csvDir = base_path(".devtools/laravel-schema-gen/csv/{$name}");
        $yamlPath = base_path(".devtools/laravel-schema-gen/schema/{$name}.yaml");

        // フォルダの存在確認
        if (!is_dir($csvDir)) {
            $this->error("CSV folder not found: {$csvDir}");
            $this->newLine();
            $this->showUsage($name);

            return self::FAILURE;
        }

        // カラム定義ファイルの存在確認
        $columnsFile = "{$csvDir}/{$name}_columns.csv";
        if (!file_exists($columnsFile)) {
            $this->error("Columns file not found: {$columnsFile}");
            $this->newLine();
            $this->showUsage($name);

            return self::FAILURE;
        }

        try {
            $converter = new CsvToYamlConverter();
            $outputPath = $converter->convert($csvDir, $yamlPath, $isAlter);

            $this->info("YAML generated: {$outputPath}");
            $this->newLine();

            // 読み込んだファイルを表示
            $this->line('<fg=yellow>Loaded files:</>');
            $this->showLoadedFiles($csvDir, $name);
            $this->newLine();

            $this->line('<fg=yellow>Next steps:</>');
            if ($isAlter) {
                $this->line("  make schema-migrate file={$name} alter=1");
            } else {
                $this->line("  make schema-migrate file={$name}");
                $this->line("  make schema-model file={$name}");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function showUsage(string $name): void
    {
        $this->line('<fg=yellow>Expected folder structure:</>');
        $this->line("  .devtools/laravel-schema-gen/csv/{$name}/");
        $this->line("    ├── {$name}_table.csv      (optional: table meta info)");
        $this->line("    ├── {$name}_columns.csv    (required: column definitions)");
        $this->line("    ├── {$name}_indexes.csv    (optional: composite indexes)");
        $this->line("    └── {$name}_relations.csv  (optional: relations)");
        $this->newLine();

        $this->line('<fg=yellow>Examples:</>');
        $this->line('  make schema-csv file=posts');
        $this->line('  make schema-csv file=posts_add_view_count alter=1');
    }

    private function showLoadedFiles(string $csvDir, string $name): void
    {
        $files = [
            "{$name}_table.csv" => 'Table meta',
            "{$name}_columns.csv" => 'Columns',
            "{$name}_indexes.csv" => 'Indexes',
            "{$name}_relations.csv" => 'Relations',
        ];

        foreach ($files as $file => $label) {
            $path = "{$csvDir}/{$file}";
            if (file_exists($path)) {
                $this->line("  <fg=green>✓</> {$file} ({$label})");
            }
        }
    }
}
