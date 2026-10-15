<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Categoria;
use App\Models\Mueble;
use App\Models\WhatsappConfig;
use App\Models\Usuario;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createCategorias();
        $this->createWhatsappConfigs();
        $this->createAdminUser();
        $this->createMockMuebleria();
    }

    private function createCategorias(): void
    {
        $categorias = [
            ['nombre' => 'salon', 'descripcion' => 'Sofás, sillones y mesas de café', 'icono' => 'sofa-icon.svg'],
            ['nombre' => 'comedor', 'descripcion' => 'Mesas y sillas de comedor', 'icono' => 'mesa-icon.svg'],
            ['nombre' => 'dormitorio', 'descripcion' => 'Camas, clósets y burós', 'icono' => 'cama-icon.svg'],
            ['nombre' => 'oficina', 'descripcion' => 'Escritorios y sillas ejecutivas', 'icono' => 'escritorio-icon.svg'],
            ['nombre' => 'cocina', 'descripcion' => 'Muebles y accesorios de cocina', 'icono' => 'silla-icon.svg'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['nombre' => $cat['nombre']],
                ['tipo' => 'plana', 'is_active' => true] + $cat
            );
        }
    }

    private function createWhatsappConfigs(): void
    {
        WhatsappConfig::firstOrCreate(
            ['numero_telefono' => '+51987654321'],
            [
                'tipo' => 'general',
                'nombre_usuario' => 'Arkea Muebles - Andahuaylas',
                'mensaje_default' => 'Hola Arkea Muebles, me interesa el mueble: [NOMBRE]. ¿Me pueden dar más información?',
                'api_key' => null,
                'is_active' => true,
                'orden' => 1,
            ]
        );

        WhatsappConfig::firstOrCreate(
            ['numero_telefono' => '+51912345678'],
            [
                'tipo' => 'vendedor',
                'nombre_usuario' => 'Juan Pérez - Asesor de ventas',
                'mensaje_default' => 'Hola, soy Juan de Arkea Muebles. ¿En qué puedo ayudarte?',
                'api_key' => null,
                'is_active' => true,
                'orden' => 2,
            ]
        );
    }

    private function createAdminUser(): void
    {
        Usuario::firstOrCreate(
            ['email' => 'admin@arkeamuebles.com'],
            [
                'password' => Hash::make('admin123'),
                'nombre' => 'Admin',
                'apellido' => 'Arkea',
                'rol' => 'admin',
                'is_active' => true,
            ]
        );
    }

    private function createMockMuebleria(): void
    {
        if (Mueble::count() > 0) {
            return;
        }

        $muebles = [
            [
                'nombre' => 'Sofá Moderno Arkea',
                'slug' => 'sofa-moderno-arkea',
                'categoria_id' => 1,
                'precio' => 2500.00,
                'estado' => 'nuevo',
                'descripcion_corta' => 'Sofá de 3 plazas tapizado en lino premium, ideal para salas contemporáneas.',
                'descripcion_completa' => 'Elegante sofá moderno de 3 plazas con respaldo alto y cojines removibles. Perfecto para espacios contemporáneos que buscan comodidad y estilo. Tapizado en tela lino de alta resistencia con costuras reforzadas y estructura en madera de roble maciza lacada. Disponible en gris oscuro, beige y naranja. Incluye garantía de 2 años y envío disponible en Andahuaylas.',
                'materiales' => ['estructura' => 'Madera de roble lacada', 'tapizado' => 'Tela lino premium', 'acabados' => ['natural', 'teñido café']],
                'medidas' => ['alto_cm' => 85, 'ancho_cm' => 200, 'profundidad_cm' => 90, 'peso_kg' => 45],
                'especificaciones' => ['tipo_asiento' => 'tapizado', 'numero_personas' => 3, 'tipo_brazos' => 'rectangulares', 'cojines_removibles' => true],
                'informacion_adicional' => ['garantia_anios' => 2, 'envio_disponible' => true, 'disponibilidad' => 'stock', 'color_disponible' => ['gris oscuro', 'beige', 'naranja'], 'consultas_whatsapp_id' => 1],
            ],
            [
                'nombre' => 'Mesa Comedor Industrial',
                'slug' => 'mesa-comedor-industrial',
                'categoria_id' => 2,
                'precio' => 1800.00,
                'estado' => 'nuevo',
                'descripcion_corta' => 'Mesa de comedor estilo industrial para 8 personas, top de madera pino.',
                'descripcion_completa' => 'Mesa rectangular de 180 cm ideal para comedores modernos. Top de madera de pino tratado con acabado mate y estructura metálica negra en forma de H. Capacidad para 6 a 8 personas cómodamente. Perfecta para uso diario y ambientes informales. Fácil limpieza y mantenimiento.',
                'materiales' => ['estructura' => 'Metal negro tubular', 'superficie' => 'Madera de pino barnizada', 'acabados' => ['mate', 'brillo']],
                'medidas' => ['alto_cm' => 75, 'ancho_cm' => 180, 'profundidad_cm' => 90, 'peso_kg' => 35],
                'especificaciones' => ['tipo_top' => 'rectangular', 'capacidad_personas' => 8, 'tipo_patas' => 'H invertida'],
                'informacion_adicional' => ['garantia_anios' => 1, 'envio_disponible' => true, 'disponibilidad' => 'stock', 'color_disponible' => ['madera natural', 'negro carbón'], 'consultas_whatsapp_id' => 2],
            ],
            [
                'nombre' => 'Cama Queen Arkea Premium',
                'slug' => 'cama-queen-arkea-premium',
                'categoria_id' => 3,
                'precio' => 3200.00,
                'estado' => 'nuevo',
                'descripcion_corta' => 'Cama queen con cabecero tapizado, base cajonera y somier incluido.',
                'descripcion_completa' => 'Cama queen de alta calidad con cabecero amplio tapizado en tela de algodón suave. Estructura reforzada de roble y pino para máxima durabilidad. Base con 4 cajones amplios para almacenamiento y somier incluido. Ideal para matrimonios que buscan confort y estilo. Garantía de 5 años.',
                'materiales' => ['estructura' => 'Madera de roble y pino', 'tapizado' => 'Tela de algodón premium', 'acabados' => ['natural', 'blanco', 'gris oscuro']],
                'medidas' => ['alto_cm' => 120, 'ancho_cm' => 160, 'profundidad_cm' => 220, 'peso_kg' => 85],
                'especificaciones' => ['tipo_colchon' => 'queen (152x198 cm)', 'tipo_base' => 'cajonera', 'cabecero_incluido' => true, 'somier_incluido' => true],
                'informacion_adicional' => ['garantia_anios' => 5, 'envio_disponible' => false, 'disponibilidad' => 'stock', 'color_disponible' => ['natural', 'blanco', 'negro'], 'consultas_whatsapp_id' => 1],
            ],
            [
                'nombre' => 'Escritorio Ejecutivo Moderno',
                'slug' => 'escritorio-ejecutivo-moderno',
                'categoria_id' => 4,
                'precio' => 2100.00,
                'estado' => 'nuevo',
                'descripcion_corta' => 'Escritorio en L con cajón con cerradura, ideal para home office.',
                'descripcion_completa' => 'Escritorio ejecutivo en L de 160x80 cm perfecto para espacios modernos. Incluye cajón principal con cerradura y espacio amplio para monitor doble. Superficie de melamina de alta resistencia a manchas y rasguños. Patas niveladoras ajustables. Compatible con sillas ergonómicas.',
                'materiales' => ['estructura' => 'Metal y MDF', 'superficie' => 'Melamina alta resistencia', 'acabados' => ['natural', 'blanco', 'nogal']],
                'medidas' => ['alto_cm' => 75, 'ancho_cm' => 160, 'profundidad_cm' => 80, 'peso_kg' => 42],
                'especificaciones' => ['tipo_superficie' => 'en L', 'numero_cajones' => 1, 'cerradura_incluida' => true],
                'informacion_adicional' => ['garantia_anios' => 3, 'envio_disponible' => true, 'disponibilidad' => 'stock', 'color_disponible' => ['natural', 'blanco', 'nogal', 'negro'], 'consultas_whatsapp_id' => 2],
            ],
            [
                'nombre' => 'Silla Comedor Clásica',
                'slug' => 'silla-comedor-clasica',
                'categoria_id' => 5,
                'precio' => 350.00,
                'estado' => 'renovado',
                'descripcion_corta' => 'Silla de comedor con respaldo alto tapizado, resistente al agua.',
                'descripcion_completa' => 'Elegante silla de comedor con diseño clásico y comodidad ergonómica. Tapizado en tela poliéster tratada resistente al agua con costuras reforzadas. Estructura en pino macizo. Fácil limpieza y mantenimiento duradero. Disponible en beige, gris, negro y rojo vino.',
                'materiales' => ['estructura' => 'Pino macizo', 'tapizado' => 'Tela poliéster tratada', 'acabados' => ['teñido']],
                'medidas' => ['alto_cm' => 95, 'ancho_cm' => 50, 'profundidad_cm' => 52, 'peso_kg' => 4.5],
                'especificaciones' => ['tipo_respaldo' => 'alto tapizado', 'apilable' => false],
                'informacion_adicional' => ['garantia_anios' => 1, 'envio_disponible' => true, 'disponibilidad' => 'stock', 'color_disponible' => ['beige', 'gris', 'negro', 'rojo vino'], 'consultas_whatsapp_id' => 1],
            ],
        ];

        foreach ($muebles as $mueble) {
            Mueble::create($mueble);
        }
    }
}
