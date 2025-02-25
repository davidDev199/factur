<x-dashboard-layout title="Usuarios | {{ session('company')->razonSocial }}" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('dashboard'),
    ],
    [
        'name' => 'Usuarios',
    ],
]">

    <x-slot name="action">

        <x-wire-button label="Nuevo" x-on:click="$openModal('simpleModal')" blue />

    </x-slot>

    @livewire('user-table', ['branches' => $branches])

    @include('users.create')

</x-dashboard-layout>