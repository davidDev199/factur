<?php

namespace App\Console\Commands;

use App\Services\Sunat\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class FetchExchangeRate extends Command
{
    protected $signature = 'exchange:fetch';
    protected $description = 'Obtiene y guarda el tipo de cambio desde SUNAT';

    public function __construct(private ExchangeRateService $exchangeRateService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->exchangeRateService->fetchAndSave();
        $this->info('Tipo de cambio actualizado correctamente.');
    }

}
