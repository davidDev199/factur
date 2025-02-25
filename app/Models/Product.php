<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'codProducto',
        'codBarras',
        'unidad',
        'mtoValor',
        'tipAfeIgv',
        'porcentajeIgv',
        'tipSisIsc',
        'porcentajeIsc',
        'icbper',
        'factorIcbper',
        'descripcion',
        'company_id',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unidad');
    }

    public function affectation()
    {
        return $this->belongsTo(Affectation::class, 'tipAfeIgv');
    }
}
