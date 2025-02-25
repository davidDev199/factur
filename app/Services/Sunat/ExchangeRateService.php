<?php

namespace App\Services\Sunat;

use Illuminate\Support\Facades\Http;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class ExchangeRateService
{
    public function fetchAndSave()
    {
        $response = Http::get('https://api.apis.net.pe/v1/tipo-cambio-sunat');

        if ($response->successful()) {
            $data = $response->json();
            $date = Carbon::parse($data['fecha'])->format('Y-m-d');
            $rate = $data['venta'];

            ExchangeRate::updateOrCreate(
                ['date' => $date, 'currency' => 'USD'],
                ['rate' => $rate, 'source' => 'SUNAT']
            );
        }
    }
}
