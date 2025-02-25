<div x-data="data">

    <form @submit.prevent="save">

        <x-wire-modal-card title="USUARIO" name="simpleModal" width="xl">

            {{-- Si hay errores, se mostrarán aquí --}}
            <div x-show="errors.length" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">¡Ups! Algo salió mal.</strong>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    <template x-for="error in errors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

            <div>

                <div>
                    <x-label value="{{ __('Name') }}" />
                    <x-input 
                        class="block mt-1 w-full" 
                        type="text"
                        placeholder="Nombre del usuario"
                        x-model="user.name" 
                        required />
                </div>
    
                <div class="mt-4">
                    <x-label value="{{ __('Email') }}" />
                    <x-input 
                        class="block mt-1 w-full" 
                        type="email" 
                        placeholder="Correo electrónico"
                        x-model="user.email"
                        required autocomplete="username" />
                </div>

                <div class="mt-4">
                    <x-label value="{{ __('Password') }}" />
                    <x-input 
                        class="block mt-1 w-full" 
                        type="password" 
                        placeholder="Contraseña"
                        x-model="user.password" 
                        required autocomplete="new-password" />
                </div>
    
                <div class="mt-4">
                    <x-label value="{{ __('Confirm Password') }}" />
                    <x-input 
                        class="block mt-1 w-full" 
                        type="password" 
                        placeholder="Confirmar contraseña"
                        x-model="user.password_confirmation" 
                        required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-label value="Sucursal" />

                    <x-select class="block mt-1 w-full" x-model="user.branch_id">
                        <option value="">Selecciona una sucursal</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </x-select>

                </div>

            </div>

            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-wire-button flat label="Cancel" x-on:click="close" />

                <x-wire-button type="submit" primary label="Guardar" />
            </x-slot>

        </x-wire-modal-card>
    </form>

</div>

@push('js')
    <script>
        function data() {
            return {
                errors: [],

                user: {
                    name: "",
                    email: "",
                    password: "",
                    password_confirmation: "",
                    branch_id: ""
                },

                save() {

                    axios.post('/users', this.user).then(response => {

                        $closeModal('simpleModal');

                        Livewire.dispatch('UserAdded');

                        this.errors = [];
                        
                        this.user = {
                            name: "",
                            email: "",
                            password: "",
                            password_confirmation: "",
                            branch_id: ""
                        };

                    }).catch(error => {

                        if (error.response.status === 422) {
                            this.errors = Object.values(error.response.data.errors).flat();
                            console.log(error.response.data);
                        }

                    });
                }
            }
        }
    </script>
@endpush