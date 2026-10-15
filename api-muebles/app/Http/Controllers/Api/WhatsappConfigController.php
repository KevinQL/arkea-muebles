<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsappConfig;
use Illuminate\Http\Request;

class WhatsappConfigController extends Controller
{
    public function index()
    {
        $configs = WhatsappConfig::where('is_active', true)
            ->orderBy('orden')
            ->get();

        return response()->json($configs);
    }

    public function config()
    {
        return response()->json(WhatsappConfig::orderBy('orden')->get());
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'configs' => 'required|array',
            'configs.*.id' => 'required|exists:whatsapp_configs,id',
            'configs.*.numero_telefono' => 'required|string|max:20',
            'configs.*.mensaje_default' => 'nullable|string',
            'configs.*.api_key' => 'nullable|string',
            'configs.*.is_active' => 'boolean',
        ]);

        foreach ($datos['configs'] as $config) {
            WhatsappConfig::where('id', $config['id'])->update($config);
        }

        return response()->json(WhatsappConfig::orderBy('orden')->get());
    }
}
