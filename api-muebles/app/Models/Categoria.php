<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'tipo',
        'subcategoria',
        'descripcion',
        'icono',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function muebles(): HasMany
    {
        return $this->hasMany(Mueble::class, 'categoria_id');
    }
}
