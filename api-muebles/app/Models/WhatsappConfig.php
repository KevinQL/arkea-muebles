<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConfig extends Model
{
    protected $table = 'whatsapp_configs';

    protected $fillable = [
        'tipo',
        'numero_telefono',
        'nombre_usuario',
        'mensaje_default',
        'api_key',
        'is_active',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
        'is_active' => 'boolean',
    ];

    public function waLink(?string $producto = null): string
    {
        $numero = preg_replace('/[^0-9]/', '', $this->numero_telefono);
        $mensaje = $this->mensaje_default ?? 'Hola Arkea Muebles';

        if ($producto !== null && $producto !== '') {
            $mensaje = str_replace('[NOMBRE]', $producto, $mensaje);
        }

        return 'https://wa.me/' . $numero . '?text=' . urlencode($mensaje);
    }
}
