<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especialidad extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function profesionales()
    {
        return $this->belongsToMany(User::class, 'especialidad_user');
    }
}
