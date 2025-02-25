<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('branches.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'code' => str_pad($request->code, 4, '0', STR_PAD_LEFT),
        ]);

        $data = $request->validate([
            'name' => 'required',
            'code' => [
                'required',
                'numeric',
                'max:4',
                Rule::unique('branches', 'code')
                    ->where('company_id', session('company')->id),
            ],
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'website' => 'nullable',
            'address' => 'required',
            'ubigeo' => 'required',
        ]);

        $data['company_id'] = session('company')->id;

        $branch = Branch::create($data);

        $branch->users()->attach(auth()->id(), [
            'company_id' => session('company')->id,
        ]);

        session('swal', [
            'icon' => 'success',
            'title' => 'Sucursal creada',
            'text' => 'La sucursal se ha creado correctamente.',
        ]);

        return redirect()->route('branches.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($branchId)
    {
        $branch = Branch::where('company_id', session('company')->id)
            ->findOrFail($branchId);
            
        return view('branches.edit', compact('branch'));
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        //
    }

    public function series($branchId)
    {
        $branch = Branch::where('company_id', session('company')->id)
            ->findOrFail($branchId);

        return view('branches.series', compact('branch'));
    }
}
