<?php

namespace App\Livewire;

use App\Models\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientCreate extends Component
{
    public $identities;
    public $openModal = false;

    public $client = [
        'tipoDoc' => '-',
        'numDoc' => null,
        'rznSocial' => null,
        'direccion' => null,
        'email' => null,
        'telephone' => null,
    ];

    public function save(){
        $this->validate([
            'client.tipoDoc' => 'required|exists:identities,id',
            'client.numDoc' => [
                Rule::requiredIf($this->client['tipoDoc'] != '-'),
                Rule::when($this->client['tipoDoc'] == 1, 'numeric|digits:8'),
                Rule::when($this->client['tipoDoc'] == 6, ['numeric','digits:11','regex:/^(10|20)\d{9}$/']),
                Rule::unique('clients', 'numDoc')->where(function($query){
                    return $query->where('company_id', session('company')->id)
                        ->where('tipoDoc', $this->client['tipoDoc'])
                        ->where('tipoDoc', '!=', '-');
                }),
            ],
            'client.rznSocial' => 'required',
            'client.direccion' => Rule::requiredIf($this->client['tipoDoc'] == 6),
            'client.telephone' => 'nullable',
            'client.email' => 'nullable',
        ]);

        $this->client['company_id'] = session('company')->id;

        $client = Client::create($this->client);

        $this->reset('client', 'openModal');

        $this->dispatch('clientAdded', $client->id);
    }

    public function searchDocument()
    {
        $this->validate([
            'client.tipoDoc' => 'required|in:1,6',
            'client.numDoc' => [
                Rule::when($this->client['tipoDoc'] == 1, 'numeric|digits:8'),
                Rule::when($this->client['tipoDoc'] == 6, ['numeric','digits:11','regex:/^(10|20)\d{9}$/']),
            ],
        ]);
        $token = 'cGVydWRldnMucHJvZHVjdGlvbi5maXRjb2RlcnMuNjdkMDgwMjg5ZmE0MTczZjYxMzIwODZk';
        $response = null;
        if ($this->client['tipoDoc'] == 1) { // DNI
            $url = 'https://api.perudevs.com/api/v1/dni/complete?document=' . $this->client['numDoc'] . '&key=' . $token;
        } elseif ($this->client['tipoDoc'] == 6) { // RUC
            $url = 'https://api.perudevs.com/api/v1/ruc?document=' . $this->client['numDoc'] . '&key=' . $token;
        } else {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Tipo de documento no válido',
            ]);
            return;
        }

        try {
            $response = Http::get($url);
            $data = $response->json();
            Log::info('Consulta de documento: ', [
                'url' => $url,
                'response' => $data,
            ]);
            Log::info($data['resultado']['razon_social'] ?? null);
            Log::info($data['resultado']['nombre_completo'] ?? null);
            Log::info($data['resultado']['direccion'] ?? null);
        } catch (\Exception $e) {
            Log::error('Error al consultar el documento: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo conectar con el servicio',
            ]);
            return;
        }

        if ($response && $data['resultado']) {
            if ($this->client['tipoDoc'] == 1) { // DNI
                $this->client['rznSocial'] = $data['resultado']['nombre_completo'] ?? null;
            } elseif ($this->client['tipoDoc'] == 6) { // RUC
                $this->client['rznSocial'] = $data['resultado']['razon_social'] ?? null;
            }
            $this->client['direccion'] = $data['resultado']['direccion'] ?? null;
        } else {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se encontró la información del documento',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.client-create');
    }
}
