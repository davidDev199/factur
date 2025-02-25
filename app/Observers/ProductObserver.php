<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product)
    {
        

        if (!app()->runningInConsole()) {
            $company_id = session('company')->id;
            $product->company_id = $company_id;
            $product->order = Product::where('company_id', $company_id)->max('order') + 1;
        }else{
            $product->order = Product::where('company_id', $product->company_id)->max('order') + 1;
        }
    }
}
