<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Receta extends Model
{
    use SoftDeletes;

    protected $table = 'recetas';

    protected $fillable = ['informe_id', 'archivo', 'nombre_original', 'tipo_mime'];

    public function informe()
    {
        return $this->belongsTo(Informe::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->tipo_mime ?? '', 'image/');
    }

    public function isPdf(): bool
    {
        return $this->tipo_mime === 'application/pdf';
    }

    public function url(): string
    {
        return asset('storage/' . $this->archivo);
    }

    public function absolutePath(): string
    {
        return Storage::disk('public')->path($this->archivo);
    }
}
