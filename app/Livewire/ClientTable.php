<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class ClientTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');

        $this->setConfigurableAreas([
            'after-wrapper' => ['clients.modal'],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Tipo Doc", "identity.description")
                ->sortable(),

            Column::make("Num. Doc.", "numDoc")
                ->searchable()
                ->sortable()
                ->format(function($value) {
                    return $value ? $value : 'S/N';
                }),

            Column::make("Razon Social", "rznSocial")
                ->searchable()
                ->sortable(),

            Column::make('actions')
                ->label(function($row) {
                    return view('clients.actions', ['client' => $row]);
                }),
            
        ];
    }

    #[On('clientAdded')]
    public function builder(): Builder
    {
        return Client::query()
            ->where('company_id', session('company')->id);
    }

    //Configuracion adicional
    public $identities;

    public $openModal = false;
    public $client_id;

    public $client = [
        'tipoDoc' => '-',
        'numDoc' => null,
        'rznSocial' => null,
        'direccion' => null,
        'email' => null,
        'telephone' => null,
    ];

    public function searchDocument()
    {
        $this->validate([
            'client.tipoDoc' => 'required|in:1,6',
            'client.numDoc' => [
                Rule::when($this->client['tipoDoc'] == 1, 'numeric|digits:8'),
                Rule::when($this->client['tipoDoc'] == 6, ['numeric','digits:11','regex:/^(10|20)\d{9}$/']),
            ],
        ]);

        $sunat = new \jossmp\sunat\ruc();
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

    public function edit(Client $client)
    {
        $this->client_id = $client->id;
        $this->openModal = true;

        $this->client = [
            'tipoDoc' => $client->tipoDoc,
            'numDoc' => $client->numDoc,
            'rznSocial' => $client->rznSocial,
            'direccion' => $client->direccion,
            'email' => $client->email,
            'telephone' => $client->telephone,
        ];

    }

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
                })->ignore($this->client_id),
            ],
            'client.rznSocial' => 'required',
            'client.direccion' => Rule::requiredIf($this->client['tipoDoc'] == 6),
            'client.telephone' => 'nullable',
            'client.email' => 'nullable',
        ]);

        $this->client['company_id'] = session('company')->id;

        Client::find($this->client_id)->update($this->client);

        $this->reset('client', 'client_id', 'openModal');
    }

    public function destroy(Client $client)
    {
        $client->delete();
    }
}
