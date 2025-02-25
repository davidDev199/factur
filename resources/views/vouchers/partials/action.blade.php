<div>
    <div class="absolute">
        <x-wire-dropdown>
            <x-slot name="trigger">
                <i class="fa-solid fa-bars"></i>
            </x-slot>
            <x-wire-dropdown.item label="Enviar por whatsapp" wire:click="openModalWhatsapp({{$row->id}})" />
            <x-wire-dropdown.item label="Enviar por correo" wire:click="openModalEmail({{$row->id}})" />
            {{-- <x-wire-dropdown.item label="Generar nota de crédito" />
            <x-wire-dropdown.item label="Generar nota de débito" /> --}}
            <x-wire-dropdown.item label="Anular" wire:click="voidReason({{$row->id}})" />
        </x-wire-dropdown>
    </div>
    
    <i class="fa-solid fa-bars text-white"></i>
</div>