<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::where('company_id', session('company')->id)->get();
        return view('users.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed'],
            'branch_id' => ['required', 'exists:branches,id'],
        ], [], [
            'branch_id' => 'sucursal',
        ]);

        //Buscar o crear usuario
        $user = User::firstOrCreate([
            'email' => $data['email'],
        ], [
            'name' => $data['name'],
            'password' => bcrypt($data['password']),
        ]);
        
        //Verificar si el usuario ya pertenece a la empresa
        $userCompany = DB::table('company_user')
            ->where('user_id', $user->id)
            ->where('company_id', session('company')->id)
            ->first();

        if($userCompany){

            throw ValidationException::withMessages([
                'email' => 'El usuario ya pertenece a la empresa.',
            ]);

        }

        //Asignar usuario a la empresa
        $user->companies()->attach(session('company')->id);

        //Asignar usuario a la sucursal
        $user->branches()->attach($data['branch_id'], [
            'company_id' => session('company')->id,
        ]);

        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
