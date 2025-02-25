<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'phone',
        'email',
        'website',
        'ubigeo',
        'address',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'branch_company_document')
            ->withPivot('serie', 'correlativo')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_company_user')
            ->withPivot('company_id')
            ->withTimestamps();
    }
}
