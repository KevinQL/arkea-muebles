<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount(['muebles as total_muebles' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:plana,jerarquica',
            'subcategoria' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        return response()->json(Categoria::create($datos), 201);
    }

    public function update(Request $request, int $id)
    {
        $categoria = Categoria::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'tipo' => 'sometimes|in:plana,jerarquica',
            'subcategoria' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $categoria->update($datos);

        return response()->json($categoria->fresh());
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update(['is_active' => false]);

        return response()->json(['message' => 'Categoría desactivada']);
    }
}
