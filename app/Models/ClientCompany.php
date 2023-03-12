<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employees_count',
        'linkedin_page_url',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
