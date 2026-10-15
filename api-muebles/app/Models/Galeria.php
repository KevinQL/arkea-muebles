<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Galeria extends Model
{
    protected $table = 'galerias';

    protected $fillable = [
        'mueble_id',
        'url_foto',
        'orden',
        'is_principal',
        'is_active',
    ];

    protected $casts = [
        'orden' => 'integer',
        'is_principal' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function mueble(): BelongsTo
    {
        return $this->belongsTo(Mueble::class, 'mueble_id');
    }
}
