<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipoDoc',
        'numDoc',
        'rznSocial',
        'address',
        'direccion',
        'phone',
        'email',
        'company_id'
    ];

    public function identity()
    {
        return $this->belongsTo(Identity::class, 'tipoDoc');
    }
}
