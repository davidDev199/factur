<?php

namespace App\Livewire;

use App\Models\Client;
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

        $config = [
            'representantes_legales' 	=> false,
            'cantidad_trabajadores' 	=> false,
            'establecimientos' 			=> false,
            'deuda' 					=> false,
        ];

        $sunat = new \jossmp\sunat\ruc($config);
        $response = $sunat->consulta($this->client['numDoc']);

        if ($response->success) {

            $this->client['rznSocial'] = $response->result->razon_social;
            $this->client['direccion'] = $response->result->direccion;

        }else{
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se encontró la empresa',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.client-create');
    }
}
