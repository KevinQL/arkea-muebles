<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $archivo = $request->file('imagen');
        $directorio = public_path('uploads/muebles');

        if (! is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $slug = Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
        $nombre = ($slug ?: 'mueble') . '-' . Str::lower(Str::random(8)) . '.' . $archivo->getClientOriginalExtension();

        $archivo->move($directorio, $nombre);

        return response()->json([
            'url' => '/uploads/muebles/' . $nombre,
        ], 201);
    }
}
