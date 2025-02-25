<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class ProductTable extends DataTableComponent
{
    //Datatable
    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setAdditionalSelects([
            'products.id as id',
            'products.company_id as company_id',
        ]);

        //Agregar vistas personalizadas
        $this->setConfigurableAreas([
            'after-wrapper' => ['products.item'],
        ]);

        $this->setDefaultSort('id', 'desc');
    }

    //Columnas
    public function columns(): array
    {
        return [
            Column::make("Codigo", "codProducto")
                ->searchable()
                ->sortable(),

            Column::make("Descripcion", "descripcion")
                ->searchable()
                ->sortable(),

            Column::make("U.M", "unit.description")
                ->sortable(),

            Column::make("Cod. Barra", "codBarras")
                ->searchable()
                ->sortable(),

            Column::make("Afectacion IGV", "affectation.description")
                ->sortable(),

            Column::make("Precio", "mtoValor")
                ->format(fn($value) => 'S/ ' . number_format($value, 2))
                ->sortable(),

            Column::make('actions')
                ->label(function ($row) {
                    return view('products.actions', ['product' => $row]);
                }),
        ];
    }

    //Define la consulta
    public function builder(): Builder
    {
        return Product::query()
            ->where('company_id', session('company')->id);
    }

    //Logica adicional
    public $affectations, $units;
    public $openModal = false;

    public $product_id;

    public $product = [
        'codProducto' => '',
        'codBarras' => '',
        'unidad' => 'NIU',
        'mtoValor' => 0,
        'precioUnitario' => 0,
        'tipAfeIgv' => 10,
        'porcentajeIgv' => 18,
        'tipSisIsc' => '',
        'porcentajeIsc' => 0,
        'icbper' => 0,
        'factorIcbper' => 0.20,
        'descripcion' => '',
    ];

    public function mount()
    {
        $this->setCode();
    }

    public function updated($property, $value)
    {
        if ($property == 'openModal') {
            if (!$value && $this->product_id) {
                $this->reset('product_id', 'product');
                $this->setCode();
            }
        }
    }

    public function setCode()
    {
        $company_id = session('company')->id;
        $codProducto = Product::where('company_id', $company_id)->max('order') + 1;

        $this->product['codProducto'] = str_pad($codProducto, 6, '0', STR_PAD_LEFT);
        $this->product['codBarras'] = str_pad($codProducto, 6, '0', STR_PAD_LEFT);
    }

    public function store()
    {
        $product = $this->product;
        $product['company_id'] = session('company')->id;

        $product = Product::create($product);

        $this->reset('product', 'openModal');
        $this->setCode();
    }

    public function edit($productId)
    {
        $product = Product::find($productId);
        $this->product = $product->only([
            'codProducto',
            'codBarras',
            'unidad',
            'mtoValor',
            'tipAfeIgv',
            'porcentajeIgv',
            'tipSisIsc',
            'porcentajeIsc',
            'icbper',
            'factorIcbper',
            'descripcion',
        ]);

        $precioUnitario = $this->product['mtoValor'] * (1 + $this->product['porcentajeIgv'] / 100);
        $this->product['precioUnitario'] = round(($precioUnitario + PHP_FLOAT_EPSILON) * 100) / 100;

        $this->product_id = $productId;
        $this->openModal = true;
    }

    public function save()
    {
        $this->validate([
            'product.codProducto' => [
                'required',
                'alpha_num',
                Rule::unique('products', 'codProducto')
                    ->ignore($this->product_id)
                    ->where(function ($query) {
                        return $query->where('company_id', session('company')->id);
                    }),
            ],
            'product.codBarras' => [
                'required',
                'alpha_num',
                Rule::unique('products', 'codBarras')
                    ->ignore($this->product_id)
                    ->where(function ($query) {
                        return $query->where('company_id', session('company')->id);
                    }),
            ],
            'product.unidad' => 'required|exists:units,id',
            'product.mtoValor' => 'required|numeric|min:0',
            'product.tipAfeIgv' => 'required|exists:affectations,id',
            'product.porcentajeIgv' => [
                'required',
                Rule::when($this->product['tipAfeIgv'] <= 17, 'in:4,10,18', 'in:0')
            ],
            'product.tipSisIsc' => 'nullable|in:01,02,03',
            'product.porcentajeIsc' => 'nullable|numeric|min:0',
            'product.icbper' => 'required|boolean',
            'product.factorIcbper' => [
                Rule::when($this->product['icbper'], 'required', 'nullable'),
                'numeric',
                'min:0',
            ],
            'product.descripcion' => 'required',
        ]);

        if (!$this->product['tipSisIsc']) {
            $this->product['porcentajeIsc'] = null;
        }

        if (!$this->product['icbper']) {
            $this->product['factorIcbper'] = null;
        }

        if ($this->product_id) {
            $product = Product::find($this->product_id);
            $product->update($this->product);
        } else {
            Product::create($this->product);
        }

        $this->reset('product', 'product_id', 'openModal');
        $this->setCode();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Producto eliminado',
            'text' => 'El producto se eliminó correctamente',
        ]);
    }
}
