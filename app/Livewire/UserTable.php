<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class UserTable extends DataTableComponent
{

    public $branches;
    public $userEdit = [
        'open' => false,
        'id' => '',
        'name' => '',
        'email' => '',
        'branch_id' => '',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setConfigurableAreas([
            'after-wrapper' => ['users.edit'],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),

            Column::make("Sucursal")
                ->label(function($row) {
                    return $row->branch->name;
                }),

            Column::make('actions')
                ->label(function($row) {
                    return view('users.actions', ['user' => $row]);
                }),
        ];
    }

    #[On('UserAdded')]
    public function builder(): Builder
    {
        return User::whereHas('companies', function ($query) {
            $query->where('company_id', session('company')->id);
        });
    }

    public function edit(User $user)
    {
        $this->userEdit = [
            'open' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'branch_id' => $user->branch->id,
        ];
    }

    public function update()
    {
        $this->validate([
            'userEdit.branch_id' => 'required|exists:branches,id',
        ],[],[
            'userEdit.branch_id' => 'sucursal',
        ]);

        DB::table('branch_company_user')
            ->where('user_id', $this->userEdit['id'])
            ->where('company_id', session('company')->id)
            ->update(['branch_id' => $this->userEdit['branch_id']]);

        $this->reset('userEdit');
    }

    public function destroy($userId)
    {
        if ($userId == auth()->id()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes eliminar tu propio usuario',
            ]);

            return;
        }

        DB::table('company_user')
            ->where('user_id', $userId)
            ->where('company_id', session('company')->id)
            ->delete();

        DB::table('branch_company_user')
            ->where('user_id', $userId)
            ->where('company_id', session('company')->id)
            ->delete();
    }
}
