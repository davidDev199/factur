<x-wire-button green wire:click="edit({{$product->id}})">
    <i class="fas fa-edit"></i>
</x-wire-button>

<x-wire-button red onclick="confirmDelete({{$product->id}})">
    <i class="fas fa-trash"></i>
</x-wire-button>