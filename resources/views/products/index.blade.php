<x-dashboard-layout title="Productos | {{ session('company')->razonSocial }}" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('dashboard'),
    ],
    [
        'name' => 'Productos',
    ],
]">

    <x-slot name="action">

        <x-wire-button label="Nuevo" x-on:click="$openModal('simpleModal')" blue />

    </x-slot>

    @livewire('product-table', [
        'affectations' => $affectations,
        'units' => $units,
    ])

</x-dashboard-layout>
