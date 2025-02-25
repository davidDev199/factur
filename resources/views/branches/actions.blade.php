<div class="w-36">
    <x-wire-button blue href="{{route('branches.edit', $branch)}}">
        Editar
    </x-wire-button>
    <x-wire-button green href="{{route('branches.series', $branch)}}">
        Series
    </x-wire-button>
    <x-wire-button red>
        Eliminar
    </x-wire-button>
</div>