<?php

namespace App\Console\Commands;

use App\Services\CsvImportService;
use Illuminate\Console\Command;

class ImportCsvCommand extends Command
{
    protected $signature = 'kafe:import-csv {file? : Path ke file CSV opsional}';

    protected $description = 'Import data bahan baku, menu, dan resep dari file CSV';

    public function handle(CsvImportService $service): int
    {
        $filePath = $this->argument('file');

        if ($filePath) {
            $this->info("Mengimpor dari file {$filePath}...");
            $hasil = $service->import($filePath);
        } else {
            $this->info('Mengimpor data CSV bawaan (database/data/kafe_data.csv)...');
            $hasil = $service->importDefault();
        }

        if ($hasil['ok']) {
            $this->info($hasil['message']);
            return Command::SUCCESS;
        }

        $this->error($hasil['message']);
        return Command::FAILURE;
    }
}

