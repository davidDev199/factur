<div class="w-8">
    <x-wire-button green wire:click="edit({{$client->id}})">
        <i class="fas fa-edit"></i>
    </x-wire-button>

    <x-wire-button red onclick="confirmDelete({{$client->id}})">
        <i class="fas fa-trash"></i>
    </x-wire-button>
</div>