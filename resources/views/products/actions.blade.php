<x-wire-button green wire:click="edit({{$product->id}})">
    <i class="fas fa-edit"></i>
</x-wire-button>

<x-wire-button red onclick="confirmDelete({{$product->id}})">
    <i class="fas fa-trash"></i>
</x-wire-button>


@push('js')
<script>
    function confirmDelete(ProductId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Sí, bórralo!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('destroy', ProductId);
            }
        });
    }
</script>
@endpush