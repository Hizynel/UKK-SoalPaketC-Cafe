<?php

namespace App\Console\Commands;

use App\Services\SupplierPriceService;
use Illuminate\Console\Command;

class SyncSupplierPriceCommand extends Command
{
    protected $signature = 'supplier:sync-price';

    protected $description = 'Perbarui harga bahan baku dari API Supplier';

    public function handle(SupplierPriceService $service): int
    {
        $this->info('Menghubungi API Supplier...');
        $result = $service->sync();

        if ($result['ok']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        }

        $this->error($result['message']);
        return Command::FAILURE;
    }
}

