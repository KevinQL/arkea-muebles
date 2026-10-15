<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mueble extends Model
{
    protected $table = 'muebles';

    protected $fillable = [
        'nombre',
        'slug',
        'categoria_id',
        'precio',
        'estado',
        'ubicacion',
        'descripcion_corta',
        'descripcion_completa',
        'galeria_principal',
        'galeria_subida',
        'galeria',
        'materiales',
        'medidas',
        'especificaciones',
        'informacion_adicional',
        'is_active',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'galeria' => 'array',
        'materiales' => 'array',
        'medidas' => 'array',
        'especificaciones' => 'array',
        'informacion_adicional' => 'array',
        'is_active' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function galerias(): HasMany
    {
        return $this->hasMany(Galeria::class, 'mueble_id')->orderBy('orden');
    }
}
