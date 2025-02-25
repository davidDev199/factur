<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::all();
        $affectations = Affectation::all();

        return view('products.index', compact(
            'units',
            'affectations',
        ));
    }
}
