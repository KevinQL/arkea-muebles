<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mueble;
use Illuminate\Http\Request;

class MuebleController extends Controller
{
    public function index(Request $request)
    {
        $query = Mueble::with(['categoria:id,nombre,tipo,subcategoria,icono,thumbnail'])
            ->where('is_active', true);

        if ($request->filled('categoria')) {
            $query->whereHas('categoria', fn ($q) => $q->where('nombre', $request->categoria));
        }

        if ($request->filled('buscador')) {
            $termino = $request->buscador;
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('descripcion_corta', 'LIKE', "%{$termino}%")
                    ->orWhere('descripcion_completa', 'LIKE', "%{$termino}%");
            });
        }

        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float) $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $muebles = $query->orderBy('id', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json($muebles);
    }

    public function show(string $idOrSlug)
    {
        $query = Mueble::with(['categoria', 'galerias']);

        if (ctype_digit($idOrSlug)) {
            $mueble = $query->findOrFail((int) $idOrSlug);
        } else {
            $mueble = $query->where('slug', $idOrSlug)->where('is_active', true)->firstOrFail();
        }

        return response()->json($mueble);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|unique:muebles,slug',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:nuevo,usado,renovado,agendado',
            'ubicacion' => 'string|max:255',
            'descripcion_corta' => 'nullable|string',
            'descripcion_completa' => 'nullable|string',
            'galeria_principal' => 'nullable|string',
            'galeria_subida' => 'nullable|string|max:255',
            'galeria' => 'nullable|array',
            'materiales' => 'nullable|array',
            'medidas' => 'nullable|array',
            'especificaciones' => 'nullable|array',
            'informacion_adicional' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $mueble = Mueble::create($datos);

        return response()->json($mueble, 201);
    }

    public function update(Request $request, int $id)
    {
        $mueble = Mueble::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:muebles,slug,' . $mueble->id,
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:nuevo,usado,renovado,agendado',
            'ubicacion' => 'sometimes|string|max:255',
            'descripcion_corta' => 'nullable|string',
            'descripcion_completa' => 'nullable|string',
            'galeria_principal' => 'nullable|string',
            'galeria_subida' => 'nullable|string|max:255',
            'galeria' => 'nullable|array',
            'materiales' => 'nullable|array',
            'medidas' => 'nullable|array',
            'especificaciones' => 'nullable|array',
            'informacion_adicional' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $mueble->update($datos);

        return response()->json($mueble->fresh());
    }

    public function destroy(int $id)
    {
        $mueble = Mueble::findOrFail($id);
        $mueble->delete();

        return response()->json(['message' => 'Mueble eliminado']);
    }
}
