<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;

/**
 * @class INFRASTOCKSuppliesSeeder
 * @brief Seeder para crear los insumos iniciales del módulo INFRASTOCK.
 *
 * Este seeder crea todos los insumos con sus características, cantidades,
 * unidades de medida y categorías correspondientes.
 */
class INFRASTOCKSuppliesSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     * Crea todos los insumos con sus datos correspondientes.
     *
     * @return void
     */
    public function run(): void
    {
        // Obtener el primer labor e inventory disponibles
        $defaultLabor = Labor::first();
        $defaultInventory = Inventory::first();

        if (!$defaultLabor) {
            $this->command->error('No hay labores disponibles. Por favor, cree al menos una labor antes de ejecutar este seeder.');
            return;
        }

        if (!$defaultInventory) {
            $this->command->error('No hay inventarios disponibles. Por favor, cree al menos un inventario antes de ejecutar este seeder.');
            return;
        }

        // Obtener todas las categorías para mapeo rápido
        $categories = InfrastockCategory::where('type', 'supply')->get()->keyBy('name');

        // Función helper para obtener o crear categoría
        $getCategory = function($categoryName) use ($categories) {
            if (empty($categoryName)) {
                return null;
            }
            if (!$categories->has($categoryName)) {
                $category = InfrastockCategory::create([
                    'name' => $categoryName,
                    'type' => 'supply'
                ]);
                $categories->put($categoryName, $category);
            }
            return $categories->get($categoryName);
        };

        // Función helper para limpiar valores numéricos
        $cleanNumber = function($value) {
            if (empty($value) || $value === 'N/A' || $value === '#VALOR!') {
                return 0;
            }
            return (int) preg_replace('/[^0-9]/', '', $value);
        };

        // Array de insumos: [nombre, categoria, caracteristicas, cantidad_inicial, consumos, cantidad_restante, unidad_medida]
        $supplies = $this->getSuppliesData();

        $created = 0;
        $updated = 0;

        foreach ($supplies as $supply) {
            $name = trim($supply[0]);
            $categoryName = trim($supply[1] ?? '');
            $characteristics = trim($supply[2] ?? '');
            $initialAmount = $cleanNumber($supply[3] ?? 0);
            $consumos = $cleanNumber($supply[4] ?? 0);
            $cantidadRestante = $cleanNumber($supply[5] ?? 0);
            $unitMeasure = trim($supply[6] ?? 'Unidad');
            $observations = trim($supply[7] ?? '');

            // Si no hay cantidad inicial pero hay cantidad restante, usar cantidad restante
            if ($initialAmount == 0 && $cantidadRestante > 0) {
                $initialAmount = $cantidadRestante;
            }

            // Obtener categoría
            $category = $getCategory($categoryName);
            if (!$category && !empty($categoryName)) {
                $this->command->warn("Categoría '{$categoryName}' no encontrada para insumo '{$name}'. Se creará sin categoría.");
            }

            // Crear o actualizar el insumo
            $equipment = Equipment::updateOrCreate(
                [
                    'name' => $name,
                    'labor_id' => $defaultLabor->id,
                    'inventory_id' => $defaultInventory->id,
                ],
                [
                    'name' => $name,
                    'category_id' => $category ? $category->id : null,
                    'characteristics' => $characteristics ?: null,
                    'initial_amount' => $initialAmount,
                    'amount' => $cantidadRestante > 0 ? $cantidadRestante : $initialAmount,
                    'unit_measure' => $unitMeasure ?: 'Unidad',
                    'price' => 0,
                    'observations' => $observations ?: null,
                    'labor_id' => $defaultLabor->id,
                    'inventory_id' => $defaultInventory->id,
                ]
            );

            if ($equipment->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Insumos procesados: {$created} creados, {$updated} actualizados.");
    }

    /**
     * Retorna el array de datos de insumos.
     * 
     * @return array
     */
    private function getSuppliesData(): array
    {
        return [
            ['Abrazadera de 1 y 2 ojos para tubo de 3/4 y pulgada', 'Accesorio de plomería', 'Viene en tamaños para tubos de 3/4 pulgada y 1 pulgada, y puede tener 1 ojo (un solo tornillo de sujeción) o 2 ojos (dos tornillos para mayor firmeza).', 46, 23, 23, 'Unidad', ''],
            ['Adaptador de 2 a 1', 'Accesorio de plomería', 'Adaptador material PVC, reducción de 2 pulgadas a 1 pulgada, presión agua potable, color blanco.', 5, 0, 5, 'Unidad', ''],
            ['Adaptador hembra de 2 pulgadas', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 2 pulgadas, presión agua potable, color blanco.', 1, 1, 0, 'Unidad', ''],
            ['Adaptador hembra de 3 pulgadas', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 3 pulgadas, presión agua potable, color blanco.', 3, 0, 3, 'Unidad', ''],
            ['Adaptador hembra de 3/4', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 3/4 pulg., presión agua potable, color blanco.', 61, 3, 58, 'Unidad', ''],
            ['Adaptador hembra de media', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 1/2 pulg., presión agua potable, color blanco.', 50, 6, 44, 'Unidad', ''],
            ['Adaptador hembra de pulgada y media', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 1-1/2 pulg., presión agua potable, color blanco.', 40, 0, 40, 'Unidad', ''],
            ['adaptador hembras de pulgada', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 1 pulgada, presión agua potable, color blanco.', 72, 0, 72, 'Unidad', ''],
            ['adaptador hembras de pulgada y cuarto', 'Accesorio de plomería', 'Adaptador hembra material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 27, 0, 27, 'Unidad', ''],
            ['Adaptador macho de 2 pulgadas', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 2 pulgadas, presión agua potable, color blanco.', 4, 1, 3, 'Unidad', ''],
            ['Adaptador macho de 3 pulgadas', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 3 pulgadas, presión agua potable, color blanco.', 1, 1, 0, 'Unidad', ''],
            ['adaptador macho de media', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 1/2 pulg., presión agua potable, color blanco.', 2, 0, 2, 'Unidad', ''],
            ['adaptador macho de pulgada', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 1 pulgada, presión agua potable, color blanco.', 45, 4, 41, 'Unidad', ''],
            ['Adaptador macho de pulgada y cuarto', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 43, 0, 43, 'Unidad', ''],
            ['Adaptador macho de pulgada y media', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 1-1/2 pulg., presión agua potable, color blanco.', 26, 1, 25, 'Unidad', ''],
            ['Adaptador machos de 3/4', 'Accesorio de plomería', 'Adaptador macho material PVC, diámetro de 3/4 pulg., presión agua potable, color blanco.', 115, 2, 113, 'Unidad', ''],
            ['Adaptador pf', 'Accesorio de plomería', 'Adaptador material PVC presión, conexión estándar PF, diámetro de 1 pulgada, color blanco.', 202, 0, 202, 'Unidad', ''],
            ['Adaptador pf codo', 'Accesorio de plomería', 'Codo adaptador material PVC presión, conexión estándar PF, diámetro de 1 pulgada, color blanco.', 11, 0, 11, 'Unidad', ''],
            ['Amarre para Zinc', 'Accesorio de fijación', 'Amarre para Zinc, material metálico, tamaño 36 cm', 150, 50, 100, 'Unidad', ''],
            ['Bisagra', 'Accesorio de fijación', 'Bisagra común, tamaño de 4 x 3 plg Pulg., material acero inoxidable, calibre 18', 30, 0, 30, 'Unidad', ''],
            ['Bisturí o Cortador Tipo Industrial', 'Herramienta de corte', 'De hoja de plexiglás o acrílico. Color: amarillo. Longitud del cortador de acrílico: 6.3 pulg. Longitud de la hoja del cuchillo: 1.8 in, ancho: 0.4 in. Incluye 10 cuchillas de corte', 1, 0, 1, 'Unidad', ''],
            ['Nylo', '', '100 gramos', 5, 0, 5, 'Unidad', ''],
            ['Bombillo Led', 'Material eléctrico', 'Bombillo led A60 9w 810 LM, tipo rosca e27, luz fría, tensión de operación 110 v, no dimerizable. paquete por 10 unidades', 38, 31, 7, 'Unidad', ''],
            ['Breaker bifasico 20 AMP', 'Material eléctrico', 'Breaker, 2 polos, capacidad 20 amp, enchufable', 20, 8, 12, 'Unidad', ''],
            ['Breaker bifasico 40AMP', 'Material eléctrico', 'Breaker, 2 polos, capacidad 40amp, enchufable', 6, 0, 6, 'Unidad', ''],
            ['Brocha de Diametro 6 Pulgadas', 'Herramienta de pintura', 'Brocha de diametro 6 pulgadas, material cerda natural, cabo de plastico.', 4, 2, 2, 'Unidad', ''],
            ['Cadena', 'Accesorio de fijación', 'Cadena de acero galvanizado, resistencia a la intemperie', 1, 0, 1, 'Unidad', ''],
            ['caja de 4 circuitos', 'Material eléctrico', 'Caja metálica para distribución eléctrica, capacidad para 4 circuitos, resistente a impactos y al calor, ideal para instalaciones residenciales e industriales', 1, 0, 1, 'Unidad', ''],
            ['Caja de ganchos industrial', 'Accesorio de fijación', 'Caja con ganchos metálicos reforzados, uso en construcción y fijaciones pesadas, resistente a la intemperie.', 1, 0, 1, 'Unidad', ''],
            ['caja de remaches ciego', 'Accesorio de fijación', 'Caja con remaches ciegos de aluminio, variedad de tamaños para fijaciones permanentes, ideal para metales y plásticos.', 1, 0, 1, 'Caja', ''],
            ['cajas de sobreponer para tomas', 'Material eléctrico', 'Caja plástica para sobreponer en tomacorrientes, resistente a impactos y al calor, compatible con sistemas eléctricos de bajo voltaje.', 31, 1, 30, 'Unidad', ''],
            ['Tama Toma corriente naranja', '', '', 13, 0, 13, 'Unidad', ''],
            ['Tapa Toma corriente Trifasico', '', '', 5, 0, 5, 'Unidad', ''],
            ['Toma (receptáculo) Trifilar', '', 'CODELCA; C-014 20/ A/ 250V 60Hz/7500 w', 2, 0, 2, 'Unidad', ''],
            ['canaletas eléctricas de sobreponer', 'Material eléctrico', 'Canaletas plásticas para proteger y organizar cables eléctricos, color blanco, fácil instalación, ideal para entornos residenciales y comerciales.', 4, 0, 4, 'Unidad', ''],
            ['Candado Exterior', 'Accesorio de seguridad', 'Candado, ranura de llave cubierta para evitar el óxido, resistente a la intemperie para usos en exteriores, doble bloqueo con esferas, tamaño 51 mm, anticizalla, 3 llaves. yale', 14, 11, 3, 'Unidad', ''],
            ['Candado Ideace Duralock', 'Accesorio de seguridad', '', 1, 0, 1, 'Unidad', ''],
            ['Canecas de estuco', 'Material de construcción', 'Estuco plástico, color blanco, caneca 5 galones, usos paredes techos interior y exterior.', 8, 3, 5, 'Unidad', ''],
            ['Canecas de pintura', 'Material de construcción', '', 10, 0, 10, 'Unidad', ''],
            ['Capuchones para resortes', 'Accesorio de fijación', '', 0, 4, 0, 'Unidad', ''],
            ['Cemento Gris', 'Material de construcción', 'Gris, Bulto x 50 Kg, tipo Portlan. (Prodcuto Cemento)', 44, 33, 11, 'Bulto', ''],
            ['Boquilla Fina', '', 'Acabado liso, antihongos', 9, 0, 9, '', ''],
            ['Cemento Blanco pegacol', 'Material de construcción', 'Cemento blanco, bulto por 40 kg, uso: pega y acabados en general.', 8, 8, 0, 'Bulto', ''],
            ['Cerrojo de Seguridad', 'Accesorio de seguridad', 'Marca Yale 170 1/4 Plus / 170 M PLUS x 4 Llaves', 3, 0, 3, 'Unidad', ''],
            ['chapas de bola', 'Accesorio de seguridad', 'Chapa de bola, en acero, color niquelado, cilindro tubular, para puerta en aluminio. Cerradura alcoba Pomo Metal 587-ET S/S Escudo 65 MM Acerado', 19, 13, 6, 'Caja', ''],
            ['Chapa izquierdas', 'Accesorio de seguridad', 'Chapa de sobreponer con cerrojo de tres golpes accionado con llave por ambos lados. Caja y cantonera en acero, pestillo tirador reversible para puertas de abrir hacia la izquierda, 2 llaves en latón.', 14, 0, 14, 'Unidad', ''],
            ['Chapa Derecha', 'Accesorio de seguridad', 'Chapa de sobreponer con cerrojo de tres golpes accionado con llave por ambos lados. Caja y cantonera en acero, pestillo tirador reversible para puertas de abrir hacia la derecha, 2 llaves en latón.', 13, 0, 13, 'Unidad', ''],
            ['Mazo de Goma', '', '680 g / 24 oz. Mazo de goma mango rojo con negro, goma color Blanca', 1, 0, 1, '', ''],
            ['Chazo expansivo con cáncamo de media', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['Chazo expansivo de media', 'Accesorio de fijación', '', 0, 8, 0, 'Unidad', ''],
            ['chazos de 1/8', 'Accesorio de fijación', '', 0, 0, 0, 'Paquete', ''],
            ['chazos de 1/4', 'Accesorio de fijación', '', 0, 50, 0, '', ''],
            ['Chazos expansivos', 'Accesorio de fijación', '', 0, 0, 0, 'Caja', ''],
            ['cheques de dos pulgadas', 'Accesorio de plomería', '', 5, 0, 5, 'Unidad', ''],
            ['cheques de pulgada y cuarto', 'Accesorio de plomería', '', 5, 0, 5, 'Unidad', ''],
            ['Cheques pulgada y media', 'Accesorio de plomería', '', 15, 1, 14, 'Unidad', ''],
            ['Cincel punta', 'Herramienta de construcción', 'Cincel para construcción, tipo pala, medidas 3/4 x 10 Pulg. Incluye protección en cabeza.', 6, 3, 3, 'Unidad', ''],
            ['Cincel pala', 'Herramienta de construcción', 'Cincel para construcción, tipo pala, medidas 3/4 x 10 Pulg. Incluye protección en cabeza.', 5, 1, 4, 'Unidad', ''],
            ['cinta aislante', 'Material eléctrico', '', 24, 15, 9, 'Rollo', ''],
            ['Cinta metrica', 'Herramienta de medición', '', 1, 0, 1, 'Unidad', ''],
            ['cinta para Drywall', 'Material de construcción', '', 5, 0, 5, 'Rollo', ''],
            ['Cinta Teflón Tipo Industrial', 'Accesorio de plomería', 'Cinta Teflón, tipo industrial, tamaño 3/4 Pulg., rollo x 50 metros.', 24, 5, 19, 'Rollo', ''],
            ['codo 1/3', 'Accesorio de plomería', '', 13, 0, 13, 'Unidad', ''],
            ['codo 45 3 pulgadas', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['codo 45 pulgas y cuarto', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 17, 0, 17, 'Unidad', ''],
            ['codo 45 sanitario pulgada y media', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1-1/2 pulg., presión agua potable, color blanco.', 1, 0, 1, 'Unidad', ''],
            ['codo 90 de pulgada y media', 'Accesorio de plomería', 'Codo 90° material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 24, 0, 24, 'Unidad', ''],
            ['Codo de 1 y cuarto', 'Accesorio de plomería', '', 15, 0, 15, 'Unidad', ''],
            ['codo de 2 pulgadas', 'Accesorio de plomería', '', 13, 10, 3, 'Unidad', ''],
            ['Codo de 45 de 2 pulgadas', 'Accesorio de plomería', '', 8, 0, 8, 'Unidad', ''],
            ['codo de 45 de 4 pulgadas', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['codo de 45 grados de 3/4', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 3/4 pulg., presión agua potable, color blanco.', 70, 0, 70, 'Unidad', ''],
            ['Codo de 45 grados de pulgada y cuarto', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 10, 0, 10, 'Unidad', ''],
            ['codo de 45 media pulgada', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1/2 pulg., presión agua potable, color blanco.', 20, 0, 20, 'Unidad', ''],
            ['codo de 45 pulgada y media', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1-1/2 pulg., presión agua potable, color blanco.', 8, 0, 8, 'Unidad', ''],
            ['codo de 45 sanitario de 2 pulgadas', 'Accesorio de plomería', '', 4, 0, 4, 'Unidad', ''],
            ['codo de 90 de 2 pulgadas', 'Accesorio de plomería', 'Codo 90° material PVC, diámetro de 1-1/4 pulg., presión agua potable, color blanco.', 36, 2, 34, 'Unidad', ''],
            ['codo de 90 grados pulgada y cuarto', 'Accesorio de plomería', 'Codo 45° material PVC, diámetro de 1-1/2 pulg., presión agua potable, color blanco.', 32, 0, 32, 'Unidad', ''],
            ['codo de pulgada y media', 'Accesorio de plomería', '', 44, 0, 44, 'Unidad', ''],
            ['Codo de 1 pulgada', 'Accesorio de plomería', '', 4, 0, 4, '', ''],
            ['Codo en PVC 3/4', 'Accesorio de plomería', 'Codo material PVC 90 grados, diámetro de 3/4 Pulg., presión agua potable, color blanco.', 45, 10, 35, 'Unidad', ''],
            ['Codo galvanizados', 'Accesorio de plomería', '', 38, 4, 34, 'Unidad', ''],
            ['Tubo Galvanizado de 2 Pulgadas', '', '', 8, 0, 8, '', ''],
            ['codo sanitario de 4 pulgadas', 'Accesorio de plomería', '', 7, 5, 2, 'Unidad', ''],
            ['collarines de 2 pulgadas', 'Accesorio de plomería', '', 10, 0, 10, 'Unidad', ''],
            ['Contactor de 32 AMP', 'Material eléctrico', 'AC Contactor NC1-3210 220V 60 Hz', 2, 0, 2, 'Unidad', ''],
            ['Cuartos de galón pegamentos PVC', 'Material de construcción', '', 4, 0, 4, 'Galón', ''],
            ['cuchillas para baldosas', 'Herramienta de corte', '', 9, 0, 9, 'Paquete', ''],
            ['Curva EMT 90 Grados Conduit', 'Material eléctrico', 'Curva EMT 90 grados, en acero galvanizado, para tubería EMT. conduit de conexiones eléctricas industriales o subterráneos, norma ul 797, ansi c 80.3', 14, 6, 8, 'Unidad', ''],
            ['Disco de Corte en Piedra', 'Herramienta abrasiva', 'De corte, de 4 1/2" x 1/16" en piedra para corte de metal inoxidable y acero al carbono. (Producto - Disco de corte)', 5, 3, 2, 'Unidad', ''],
            ['Disco de corte para metal DEWALT Tipo 41', 'Herramienta abrasiva', 'Corte Metal/ Acero Inoxidable Tipo 41. 115 x 1,2 x 22,23 mm (4-1/2" x 045" x 7/8")', 5, 5, 0, 'Unidad', ''],
            ['Disco de corte para metal PRETUL Tipo 27', '', 'Disco abrasivo Tipo 27, 115 mm 4-1/2"  Ancho 6.5 mm 1/4"', 10, 0, 10, 'Unidad', ''],
            ['Disco de corte para metal PRETUL Tipo 41', '', 'Disco de corte para meta. Disco abrasivol. 115 mm 4-1/2" ancho 1 mm 3/64"', 5, 4, 1, 'Unidad', ''],
            ['Disco FLAP 4,5"', '', 'Acero + Madera, Acabado fino, Rapida remocion de material, 13.500 RPM DISCO FLAP 4.5" NO 60', 13, 0, 13, '', ''],
            ['DIsco Bauker 115 mm (4 1/2")', '', 'Disco diamantado segmentado. Para cortes de precisión en ceramica, ladrillos y azulejos. Para ser usado en esmerilles angulares (pulidoras, amoladora) con ejs de 16 mm, 22 mm y 23 mm.', 2, 0, 2, '', ''],
            ['Disco VIGOR 4-1/2" 115 mm', '', 'Disco diamantado, para concetro, piedra y bloque. Diametro exterior: 4-1/2" 115 mm. Diametro interior: 7/8" (22.23mm)', 2, 0, 2, '', ''],
            ['Flexómetro', 'Herramienta de medición', 'Caja cromada, por 8 metros con doble graduación con banda de imán para fijar punta. (Producto - Flexometro)', 10, 4, 6, 'Unidad', ''],
            ['flotadores de pulgada', 'Accesorio de plomería', '', 12, 0, 12, 'Unidad', ''],
            ['graniplast caneca', 'Material de construcción', '', 1, 1, 0, 'Unidad', ''],
            ['Gratas', 'Herramienta abrasiva', '', 3, 0, 3, 'Unidad', ''],
            ['Tablero Monofasico de 4 circuitos', '', '', 1, 0, 1, 'Unidad', ''],
            ['Interruptor sencillos', 'Material eléctrico', '', 10, 5, 5, 'Unidad', ''],
            ['Interruptor Sencillo Conmutable', '', 'Interruptores conmutable sencillos 3 vías, Material PVC alto 12 cm, ancho 2 cm, Largo 8 cm color negro, tapa incluidas', 1, 0, 1, '', ''],
            ['Interruptores dobles', 'Material eléctrico', '', 13, 0, 13, 'Unidad', ''],
            ['Kit para tanques sanitarios', 'Accesorio de plomería', '', 3, 3, 0, 'Unidad', ''],
            ['Lámpara de 1.20 x 30 panel led 48w', 'Material eléctrico', '', 1, 0, 1, 'Unidad', ''],
            ['Lámparas 60x60 48 W', 'Material eléctrico', '', 29, 7, 22, 'Unidad', ''],
            ['Lampara 60 x 60 Luminaria', 'Material eléctrico', '', 48, 0, 48, 'Unidad', 'Ubicada en el Baño bodega'],
            ['Lamparas sinflorentes (sin tubos) 1x20', 'Material eléctrico', '', 3, 0, 3, 'Unidad', ''],
            ['llave de paso 2 pulgadas', 'Accesorio de plomería', '', 18, 2, 16, 'Unidad', ''],
            ['llave de paso 3/4', 'Accesorio de plomería', '', 1, 1, 0, 'Unidad', ''],
            ['llave de paso de media pulgada', 'Accesorio de plomería', '', 10, 0, 10, 'Unidad', ''],
            ['llave de paso media pulgada', 'Accesorio de plomería', '', 0, 0, 0, 'Unidad', ''],
            ['llave de paso pulgada y cuarto', 'Accesorio de plomería', '', 8, 0, 8, 'Unidad', ''],
            ['llave de paso pulgada y media', 'Accesorio de plomería', '', 16, 0, 16, 'Unidad', ''],
            ['llave para Jardin', 'Accesorio de jardinería', '', 11, 10, 1, 'Unidad', ''],
            ['llave para lavamanos', 'Accesorio de plomería', 'Llave para lavamanos, tipo sencilla, material polímero de alta ingeniería, acabado en cromo, rago de presión Entre 20 y 125 PSI. minimo 13 cm de largo', 18, 8, 10, 'Unidad', ''],
            ['Llave Cocina para pared', '', '', 1, 0, 1, '', ''],
            ['Llave Terminal Plástica Jardín', 'Accesorio de jardinería', 'Llave terminal plástica jardín 1/2 pulgada color blanco, medidas de 9 * 10 cm.', 10, 10, 0, 'Unidad', ''],
            ['Llave Terminal de Jardin en ZINC', '', 'LLave de jardín tipo pesada 230 Gr, llave terminal en ZInc rosca manguera', 8, 8, 0, 'Unidad', ''],
            ['Mangueras de lavaplatos', 'Accesorio de plomería', 'Acople para lavamanos, material PP y PE de alta densidad, acople 1/2 Pulg. x 1/2 Pulg., x 40 cm de longitud', 52, 10, 42, 'Unidad', ''],
            ['Marcos seguetas completas', 'Herramienta de corte', 'Herramienta de corte', 3, 2, 1, 'Unidad', ''],
            ['Media', 'Material de construcción', '', 12, 0, 12, 'Unidad', ''],
            ['mezclador para lavaplatos', 'Accesorio de plomería', 'Grifo para lavaplatos, tipo cuello de ganzo, salida de agua sencilla, tamaño 8 Pulg.', 13, 2, 11, 'Unidad', ''],
            ['MT union de 3 cuartos', 'Accesorio de plomería', '', 44, 15, 29, 'Unidad', ''],
            ['Niveladores para mesa', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['Orinales', 'Material sanitario', '', 13, 0, 13, 'Unidad', ''],
            ['Buje soldado Sanitario 10"', '', 'Buje soldado sanitario 3" x 2. Accesorio en PVC', 10, 0, 10, '', ''],
            ['Nivel', '', '', 1, 0, 1, '', ''],
            ['Palustre de Construcción 8 Pulgadas', 'Herramienta de construcción', 'Palustre de construcción, material metálico, tamaño 8 Pulg., mango plástico .', 9, 1, 8, 'Unidad', ''],
            ['espatula pequeña', 'Herramienta de construcción', '', 1, 1, 0, 'Unidad', ''],
            ['panel led 18w redondas', 'Material eléctrico', 'Panel LED 18W Redonda Sobreponer. Luz Blanca', 20, 0, 20, 'Unidad', ''],
            ['panel led de 24w.', 'Material eléctrico', '', 3, 0, 3, 'Unidad', ''],
            ['Para sanitarios', 'Accesorio de plomería', '', 5, 0, 5, 'Unidad', ''],
            ['pernos de pulgada', 'Accesorio de fijación', '', 15, 0, 15, 'Unidad', ''],
            ['Pintura anticorrosivo blanco mate', 'Material de pintura', 'Pintura tipo anticorrosiva, color Blanco, acabado mate, por galón.', 4, 0, 4, 'Galón', ''],
            ['Pintura Blanca (a base de agua)', 'Material de pintura', '', 1, 1, 0, 'Galón', ''],
            ['Pintura Esmalte gamboa (Galón)', 'Material de pintura', 'Esmalte sintético a base de aceite, color gamboa, acabado brillante, por galón.', 1, 1, 0, 'Galón', ''],
            ['Pintura Esmalte Gris', 'Material de pintura', 'Pintura tipo 1, esmalte sintético a base de aceite de acabado brillante, color gris, para mantenimientos de mobiliario.', 3, 0, 3, 'Galón', ''],
            ['Pintura Esmalte negro', 'Material de pintura', 'Pintura tipo 1, esmalte sintético a base de aceite de acabado brillante, color negro, para mantenimientos de mobiliario.', 2, 0, 2, 'Galón', ''],
            ['Pintura Esmalte rojo (pintura a base de aceite rojo)', 'Material de pintura', 'Pintura tipo 1, esmalte sintético a base de aceite de acabado brillante, color rojo, por galón.', 4, 1, 3, 'Galón', ''],
            ['Pintura Esmalte verde (pintura a base de aceite verde)', 'Material de pintura', 'Pintura tipo 1, esmalte sintético a base de aceite de acabado brillante, color verde Pino, para mantenimientos de puertas.', 2, 0, 2, 'Galón', ''],
            ['Pintura Tráfico anticorrosivo blanco', 'Material de pintura', 'Pintura Demarcación Blanco 1 Galón. 6 a 8 m²/gal, a un espesor de película seca de 8 mils (200 micrones); 60 a 80 m/gal lineales.', 4, 0, 4, 'Galón', ''],
            ['Piso en Gres', 'Material de construcción', 'Piso en gres, dimensiones 25 cm x 25 cm, color colonial Betania', 30, 30, 0, 'Caja', ''],
            ['Piso en Gres Color Tablón Cucuta', 'Material de construcción', 'Piso en gres, dimensiones 30 cm x 30 cm, color tablón cucuta.', 30, 17, 13, 'Caja', ''],
            ['Baldosa', 'Material de construcción', '', 11, 0, 11, 'Caja', ''],
            ['Plafones', 'Material eléctrico', '', 4, 0, 4, 'Unidad', ''],
            ['Pliegos de lijas', 'Herramienta abrasiva', '', 22, 5, 17, 'Pliego', ''],
            ['punteros', 'Herramienta de construcción', '', 3, 0, 3, 'Unidad', ''],
            ['Puntillas', 'Accesorio de fijación', 'Puntillas metálicas, material acero', 0, 0, 0, 'Paquete', ''],
            ['Reduccion de 1 a 1/2', 'Accesorio de plomería', '', 25, 5, 20, 'Unidad', ''],
            ['Reduccion de 1 a 1/4', 'Accesorio de plomería', '', 52, 0, 52, 'Unidad', ''],
            ['reducción de 1 a 3/4', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['reducción de 1/2', 'Accesorio de plomería', '', 8, 2, 6, 'Unidad', ''],
            ['Reduccion de 1/2 a 3 cuartos', 'Accesorio de plomería', '', 10, 0, 10, 'Unidad', ''],
            ['reduccion de 2', 'Accesorio de plomería', '', 12, 1, 11, 'Unidad', ''],
            ['reducción de 2 a 1', 'Accesorio de plomería', '', 21, 1, 20, 'Unidad', ''],
            ['reducción de 2 a 1/2', 'Accesorio de plomería', '', 9, 0, 9, 'Unidad', ''],
            ['reduccion de 2 a 1/4', 'Accesorio de plomería', '', 16, 0, 16, 'Unidad', ''],
            ['Reducción de 2 a 1/4', 'Accesorio de plomería', '', 0, 0, 0, 'Unidad', ''],
            ['Reduccion de 2 a media  14', 'Accesorio de plomería', '', 14, 0, 14, 'Unidad', ''],
            ['reduccion de 3 a 2', 'Accesorio de plomería', '', 7, 0, 7, 'Unidad', ''],
            ['Reducción de 3 a 2 pulgada y media', 'Accesorio de plomería', '', 0, 0, 0, 'Unidad', ''],
            ['Reduccion de 3/4 a media', 'Accesorio de plomería', '', 79, 20, 59, 'Unidad', ''],
            ['reducción de 4  a 2', 'Accesorio de plomería', '', 13, 1, 12, 'Unidad', ''],
            ['Reducción de 4 a 3', 'Accesorio de plomería', '', 12, 0, 12, 'Unidad', ''],
            ['reduccion de pulga a media', 'Accesorio de plomería', '', 3, 0, 3, 'Unidad', ''],
            ['Reducción de pulgada a 3/4', 'Accesorio de plomería', '', 73, 0, 73, 'Unidad', ''],
            ['Reducción de pulgada a media', 'Accesorio de plomería', '', 156, 0, 156, 'Unidad', ''],
            ['Reduccion de pulgada y media a pulgada', 'Accesorio de plomería', '', 20, 0, 20, 'Unidad', ''],
            ['Reduccion de uno y media a media', 'Accesorio de plomería', '', 22, 0, 22, 'Unidad', ''],
            ['reflector led 100w', 'Material eléctrico', 'Reflector Led, 100w Ip66 8500 lúmenes, Exterior IP contra chorros de agua y salpicaduras.', 14, 11, 3, 'Unidad', ''],
            ['Reglas multitomas', 'Material eléctrico', 'Multitoma 6 salidas 3m 15 A', 3, 3, 0, 'Unidad', ''],
            ['Rejillas', 'Herramienta de construcción', '', 5, 0, 5, 'Unidad', ''],
            ['Reparaciones de pulgada y media', 'Accesorio de plomería', '', 32, 0, 32, 'Unidad', ''],
            ['Rodachina', 'Accesorio de fijación', 'Rodachina giratoria, diámetro de 1-1/2 Pulg., anclaje de 4 tornillos, resistencia 20 kg. Diametro 1.1/2\'\';Alto 5 Cm;Base 3.8X4.5 Cm Rueda En Caucho;Maximo Peso Soportado 20Kg', 10, 4, 6, 'Unidad', ''],
            ['Rodillo para Pintar', 'Herramienta de pintura', 'Rodillo para pintar, tamaño 9 Pulg., material microfibra del pelo felpa, incluye acople a escobero con mango plástico.', 26, 2, 24, 'Unidad', ''],
            ['Rollo de manguera para jardín', 'Material de jardinería', 'Tipo swank para trabajo liviano, refuerzo en nylon, presión de trabajo de 100 PSI, presión de rotura de 300 PSI, calibre de 1/2 pulgada, con acoples metálicos, rollo x 30 m.', 4, 1, 3, 'Rollo', ''],
            ['rollos cinta tapagoteras', 'Material de construcción', '', 7, 0, 7, 'Rollo', ''],
            ['rollos de cáñamo', 'Material de construcción', '', 7, 1, 6, 'Unidad', ''],
            ['semicodo de 2 Pulgadas', 'Accesorio de plomería', '', 16, 1, 15, 'Unidad', ''],
            ['semicodo de 45 grados', 'Accesorio de plomería', '', 50, 3, 47, 'Unidad', ''],
            ['sifon de 2', 'Accesorio de plomería', '', 10, 1, 9, 'Unidad', ''],
            ['sifon de 3', 'Accesorio de plomería', '', 8, 0, 8, 'Unidad', ''],
            ['Sifón Material Plástico Flexible Gris', 'Accesorio de plomería', 'Sifón, material plástico flexible gris, tipo acordeón, longitud 86 cm., para Lavaplatos/Lavamanos.', 24, 9, 15, 'Unidad', ''],
            ['sifones de 2 pulgadas sanitarios', 'Accesorio de plomería', '', 2, 0, 2, 'Unidad', ''],
            ['Silicona Multipropósito Color Transparente', 'Material de construcción', 'Silicona multipropósito, sello Juntas y vidrio, color transparente, tarro x 280 ml.', 20, 8, 12, 'Unidad', ''],
            ['Tapon con rosca  media', 'Accesorio de plomería', '', 37, 1, 36, 'Unidad', ''],
            ['Tapon de 3/4 con liso', 'Accesorio de plomería', '', 8, 0, 8, 'Unidad', ''],
            ['Tapon de 3/4 con rosca', 'Accesorio de plomería', '', 9, 0, 9, 'Unidad', ''],
            ['Tapon de media Pulgada', 'Accesorio de plomería', '', 7, 0, 7, 'Unidad', ''],
            ['tapon de prueba de 2 pulgadas', 'Accesorio de plomería', '', 4, 0, 4, 'Unidad', ''],
            ['tapon de prueba de 3 pulgadas', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['tapon de prueba de 4 pulgadas', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['tapon de pulgada lisa', 'Accesorio de plomería', '', 10, 0, 10, 'Unidad', ''],
            ['tapon de pulgada roscada', 'Accesorio de plomería', '', 13, 0, 13, 'Unidad', ''],
            ['tapon liso de 4 pulgadas', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['Tapon lisos de pulgada y media', 'Accesorio de plomería', '', 14, 0, 14, 'Unidad', ''],
            ['tapon roscados de 4 pulgadas', 'Accesorio de plomería', '', 4, 0, 4, 'Unidad', ''],
            ['Tarros de limpiador De PVC', 'Material de limpieza', 'Limpiador removedor para PVC CPVC', 5, 2, 3, 'Unidad', ''],
            ['Taza Inodoro o Sanitario', 'Material sanitario', 'Taza sanitario, material porcelana, tipo Taza Báltica Entrada Superior Con Fluxómetro Palanca', 2, 0, 2, 'Unidad', ''],
            ['Tazas sanitaria entrada posterior', 'Material sanitario', '', 3, 0, 3, 'Unidad', ''],
            ['Tee de 2 pulgadas', 'Accesorio de plomería', '', 12, 2, 10, 'Unidad', ''],
            ['Tee de 3 pulgadas Sanitario', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['Tee de media', 'Accesorio de plomería', 'Tee material PVC, diámetro de 1/2 Pulg. presión agua potable, color blanco', 133, 1, 132, 'Unidad', ''],
            ['Tee de pulgada y cuarto', 'Accesorio de plomería', 'Tee material PVC, diámetro de 1 1/4 Pulg. presión agua potable, color blanco', 23, 0, 23, 'Unidad', ''],
            ['Tee de una pulgada', 'Accesorio de plomería', 'Tee material PVC, diámetro de 1 1/4 Pulg. presión agua potable, color blanco', 25, 0, 25, 'Unidad', ''],
            ['Tee de una pulgada y media', 'Accesorio de plomería', 'Tee material PVC, diámetro de 1 1/2 Pulg. presión agua potable, color blanco', 32, 0, 32, 'Unidad', ''],
            ['Tijeras aéreas', 'Herramienta de corte', '', 8, 0, 8, 'Unidad', ''],
            ['Tijeras para jardín', 'Herramienta de jardinería', '', 6, 0, 6, 'Unidad', ''],
            ['Toalleros', 'Accesorio de hogar', '', 15, 0, 15, 'Unidad', ''],
            ['Tornillo de pulgada golosos', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['Tornillo golozo de 2 pulgadas', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['Tornillo para Drywall', 'Accesorio de fijación', 'Tornillo panel, para drywall, punta broca, dimensión 6 x 1 Pulg., paquete por 1000 Unidades.', 0, 35, 0, 'Unidad', ''],
            ['Tornillo para Drywall pulgada y media', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['Tornillo para sillas', 'Accesorio de fijación', '', 0, 0, 0, 'Unidad', ''],
            ['tubería de alcantarilla 1/2', 'Material de construcción', '', 8, 0, 8, 'Unidad', ''],
            ['Tubo cunduit plastico', 'Accesorio de plomería', '', 7, 0, 7, '', ''],
            ['Tubería EMT', 'Material eléctrico', 'Tubería EMT ¾ de pulgada por 3 metros.', 20, 7, 13, 'Unidad', ''],
            ['tubo de 90 con 3/4', 'Accesorio de plomería', '', 15, 0, 15, 'Unidad', ''],
            ['union de 1/4', 'Accesorio de plomería', '', 2, 0, 2, 'Unidad', ''],
            ['unión de 3', 'Accesorio de plomería', '', 9, 1, 8, 'Unidad', ''],
            ['union de 3 pulgadas', 'Accesorio de plomería', '', 8, 1, 7, 'Unidad', ''],
            ['union de 3/4 galvanizado', 'Accesorio de plomería', '', 10, 10, 0, 'Unidad', ''],
            ['union de 3/4', 'Accesorio de plomería', '', 51, 2, 49, 'Unidad', ''],
            ['unión de 4 pulgadas', 'Accesorio de plomería', '', 1, 1, 0, 'Unidad', ''],
            ['unión de 4 pulgadas sanitario', 'Accesorio de plomería', '', 8, 0, 8, 'Unidad', ''],
            ['Union de media', 'Accesorio de plomería', '', 75, 18, 57, 'Unidad', ''],
            ['union de reparacion 2 pulgadas', 'Accesorio de plomería', '', 6, 2, 4, 'Unidad', ''],
            ['Union de pulgada', 'Accesorio de plomería', '', 36, 9, 27, 'Unidad', ''],
            ['union de pulgada y cuarto', 'Accesorio de plomería', '', 81, 0, 81, 'Unidad', ''],
            ['union de pulgada y media', 'Accesorio de plomería', '', 25, 9, 16, 'Unidad', ''],
            ['Union de reparación de pulgada', 'Accesorio de plomería', '', 9, 0, 9, 'Unidad', ''],
            ['union de tes sanitaria', 'Accesorio de plomería', '', 2, 0, 2, 'Unidad', ''],
            ['union sanitarias pulgada y media', 'Accesorio de plomería', '', 2, 0, 2, 'Unidad', ''],
            ['universal de 2 pulgadas', 'Accesorio de plomería', '', 15, 8, 7, 'Unidad', ''],
            ['universal de 2 pulgadas y media', 'Accesorio de plomería', '', 3, 0, 3, 'Unidad', ''],
            ['universal de media', 'Accesorio de plomería', '', 3, 0, 3, 'Unidad', ''],
            ['universal de pulgada y cuarto', 'Accesorio de plomería', '', 15, 1, 14, 'Unidad', ''],
            ['universal de pulgada y media', 'Accesorio de plomería', '', 15, 0, 15, 'Unidad', ''],
            ['válvula de pie de pulgada y media plastica', 'Accesorio de plomería', '', 1, 0, 1, 'Unidad', ''],
            ['Válvulas de pie de pulga', 'Accesorio de plomería', '', 5, 0, 5, 'Unidad', ''],
            ['Yee de 1/2 sanitaria', 'Accesorio de plomería', 'Conexión en Y sanitaria, material PVC, diámetro de 1/2 pulg., color amarillo', 0, 0, 0, 'Unidad', ''],
            ['Yee de 2 pulgadas sanitaria', 'Accesorio de plomería', 'Conexión en Y sanitaria, material PVC, diámetro de 2 pulgadas, color amarillo', 4, 0, 4, 'Unidad', ''],
            ['Yee de 3 SANITARIO', 'Accesorio de plomería', 'Conexión en Y sanitaria, material PVC, diámetro de 3 pulgadas, color amarillo', 10, 0, 10, 'Unidad', ''],
            ['Yee de 4 Sanitario', 'Accesorio de plomería', 'Conexión en Y sanitaria, material PVC, diámetro de 4 pulgadas, color amarillo', 6, 0, 6, 'Unidad', ''],
            ['Lubricante', 'Material de limpieza', '', 3, 1, 2, 'Unidad', ''],
            ['Barras de Soldadura 6013', '', 'Soldadura delgada de 5 kg de 6013', 15, 0, 15, 'Kilos', ''],
            ['Barras de Soldadura 6011', '', 'Soldadura delgada de 5 kg de 6011', 90, 17, 73, 'Kilos', ''],
            ['Barras de Soldadura gruesa', 'Material de construcción', '', 80, 6, 74, 'Unidad', ''],
            ['Soldadura Liquida PVC', '', '', 2, 0, 2, 'Unidad', ''],
            ['Kit Soldador Inversor Bauker', 'Herramienta de construcción', '', 1, 0, 1, 'Unidad', ''],
            ['Yoyo para guadaña', 'Accesorio de jardinería', '', 1, 0, 1, 'Unidad', ''],
            ['Correa Tensor', '', '', 1, 0, 1, '', ''],
            ['Hilo de Yoyo', '', 'Cortador de Grasa, Con Poliamida alto peso molecular de 30 metros', 3, 0, 3, 'Unidad', ''],
            ['Cuchillas para guadaña', 'Herramienta de corte', 'Cuchilla de 35 cm de largo y 2,4 cm de ancho, con un diámetro de ojo de 25 mm. Alta resistencia, tratada térmicamente, utilizable por ambos lados y balanceada para prolongar la vida útil de tu máquina', 2, 0, 2, 'Rollo', ''],
            ['Cortaceto', '', '', 1, 0, 1, 'Unidad', ''],
            ['Guadaña', '', '', 1, 0, 1, 'Unidad', ''],
            ['Sopladora', '', '', 1, 0, 1, 'Unidad', ''],
            ['Breaker 34.100 (2x30)', 'Material eléctrico', 'Interruptor eléctrico de doble polo con capacidad de 30 amperios, ideal para circuitos de alta carga.', 10, 2, 8, 'Unidad', ''],
            ['Breaker 16 AMP', '', 'Corriente nominal 16A, Tension Nominal 415V, Frecuencia 50/60hz. Curva C', 3, 0, 3, '', ''],
            ['Breaker Ambiente 9 y 13 (2x40)', 'Material eléctrico', 'Interruptor doble polo de 40 amperios, diseñado para uso en ambientes específicos.', 6, 0, 6, 'Unidad', ''],
            ['Breaker Monofasico 15 AMP', '', '', 2, 0, 2, 'Unidad', ''],
            ['GuardaMotor 32 A', '', 'Corriente 24 - 32 A, Frecuencia 50/60 Hz, Proteccion IP20', 1, 0, 1, '', ''],
            ['Breaker Monofasico 20 AMP', '', '', 1, 0, 1, 'Unidad', ''],
            ['Breaker Casona (2x15)', 'Material eléctrico', ' Interruptor de doble polo de 15 amperios, adecuado para instalaciones residenciales pequeñas.', 38, 2, 36, 'Unidad', ''],
            ['Breaker 18.000 y 24.000 BTU (2x20)', 'Material eléctrico', 'Interruptor de 20 amperios, utilizado para aires acondicionados de alto rendimiento.', 20, 0, 20, 'Unidad', ''],
            ['Contactor A 30 trifásico', 'Material eléctrico', 'Dispositivo de control eléctrico trifásico con capacidad para 30 amperios.', 3, 1, 2, 'Unidad', ''],
            ['Breaker Trifasico de 40', 'Material eléctrico', 'Rango de Voltaje 120/415 V Unipolar/ Bipolar, Curva de disparar C', 8, 0, 8, '', ''],
            ['Contactor A 20 a 25', 'Material eléctrico', 'Contactor eléctrico para cargas de 20 a 25 amperios.', 2, 0, 2, 'Unidad', ''],
            ['Bobina Contactor 40', 'Material eléctrico', 'Contactor eléctrico de doble polo con capacidad de 40 amperios, ideal para aplicaciones industriales.', 3, 1, 2, 'Unidad', ''],
            ['Capacitor de 55 uf a 450V', 'Material eléctrico', 'Componente eléctrico de 55 microfaradios y 450 voltios, utilizado en sistemas de arranque y motores.', 2, 0, 2, 'Unidad', ''],
            ['Capacitor 20 uf', '', '', 2, 0, 2, 'Unidad', ''],
            ['Capacitor 35 Uf', '', '', 1, 0, 1, 'Unidad', ''],
            ['Capacitor de 35 microfaradios', 'Material eléctrico', 'Capacitor de capacidad media, útil en sistemas eléctricos y de aire acondicionado.', 3, 1, 2, 'Unidad', ''],
            ['Capacitor de 20 microfaradios a 450V', 'Material eléctrico', 'Capacitor de alta capacidad y voltaje, empleado en circuitos industriales.', 3, 0, 3, 'Unidad', ''],
            ['Presostato de 40 a 60 PSI', 'Material eléctrico', 'Dispositivo regulador de presión, utilizado en sistemas hidráulicos o de aire.', 2, 0, 2, 'Unidad', ''],
            ['Presostato de 30 a 50 PSI', 'Material eléctrico', 'Dispositivo regulador de presión, utilizado en sistemas hidráulicos o de aire.', 2, 0, 2, 'Unidad', ''],
            ['Recubrimiento térmico Rubatex 1x80', 'Material de construcción', 'Material aislante térmico en formato de rollo de 1x80, para tuberías o conductos.', 4, 0, 4, 'Unidad', ''],
            ['Cinta vinilo', 'Material eléctrico', 'Cinta adhesiva resistente, utilizada para aislamiento y protección de cables.', 15, 0, 15, 'Rollo', ''],
            ['Pinza amperimétrica', 'Herramienta de medición', 'Herramienta de medición eléctrica para calcular corriente sin contacto directo.', 1, 0, 1, 'Unidad', ''],
            ['Juego de destornilladores dieléctricos', 'Herramienta de construcción', 'Conjunto de destornilladores aislados para trabajos en circuitos eléctricos.', 1, 0, 1, 'Unidad', ''],
            ['Tiras de Rubatex', 'Material de construcción', 'Tiras de material aislante para recubrimiento térmico en tuberías.', 5, 1, 4, 'Unidad', ''],
            ['Lima', '', '', 5, 0, 5, '', ''],
            ['Juego de bistrol', 'Herramienta de corte', 'Conjunto de cuchillas o cortadores de precisión, ideales para tareas detalladas.', 1, 0, 1, 'Unidad', ''],
            ['Alicate de 8 pulgadas', 'Herramienta de medición', 'Herramienta manual para sujeción y corte en aplicaciones eléctricas o mecánicas.', 2, 1, 1, 'Unidad', ''],
            ['Careta para soldar', 'Accesorio de seguridad', '', 1, 0, 1, 'Unidad', ''],
            ['Corta frío', 'Herramienta de construcción', 'Herramienta de impacto para cortar o fracturar materiales duros como concreto o metal.', 1, 0, 1, 'Unidad', ''],
            ['Cascos de protección industrial - blanco', 'Accesorio de seguridad', 'Casco de protección industrial, color blanco, polietileno, con suspensión ajustable, certificado ANSI Z89.1.', 5, 0, 5, 'Unidad', ''],
            ['Amarras plasticas', 'Accesorio de fijación', '', 0, 0, 0, '', ''],
            ['Rastrillos Verdes', 'Herramienta de jardinería', '', 20, 8, 12, 'Unidad', ''],
            ['Cinta Color Azul', 'Material de construcción', '', 4, 4, 0, 'Unidad', ''],
            ['Cinta Color Rojo', 'Material de construcción', '', 4, 4, 0, 'Unidad', ''],
            ['Cinta Color Amarillo', 'Material de construcción', '', 4, 3, 1, 'Unidad', ''],
            ['Cinta Color Verde', 'Material de construcción', '', 4, 4, 0, 'Unidad', ''],
            ['Tubo Electrico Metalico 2"', '', 'Tubo Electrico metalico de 2", conduit', 8, 0, 8, 'Unidad', 'TIC'],
            ['Tubo Electrico Metalico 1"', '', 'Tubo Electrico metalico de 2", conduit', 5, 0, 5, 'Unidad', 'TIC'],
            ['Tubo Electrico Metalico 3/4', '', 'Tubo Electrico metalico de 3/4, conduit', 10, 0, 10, 'Unidad', 'TIC'],
            ['Canaleta y Tapas', '', '', 0, 0, 0, 'Unidad', 'TIC'],
            ['Tablero Electrico', '', '', 1, 0, 1, 'Unidad', 'TIC'],
            ['Suitable Wet Location', '', '', 1, 0, 1, 'Unidad', 'TIC'],
            ['Curva conduit 3/4', '', 'Curva electrico metalico de 3/4, Conduit', 2, 0, 2, 'Unidad', 'TIC'],
            ['Toma Corriente', '', '', 4, 0, 4, 'Unidad', 'TIC'],
            ['Union de 2" Condouit', '', 'Union electrico metalico de 2", Conduit', 5, 0, 5, 'Unidad', 'TIC'],
            ['Abrazadera para tubo', '', 'Abrazadera metalica', 5, 0, 5, 'Unidad', 'TIC'],
            ['Tubo cunduit plastico 26mm 3/4', '', '', 22, 0, 22, 'Unidad', 'TIC'],
            ['Tubo cunduit plastico 21mm', '', '', 6, 0, 6, 'Unidad', 'TIC'],
            ['Tubo Agua Apresion de 1 1/4"', '', 'Tubo agua presion PVC, 42 mm, 1" 1/4 200 PSI', 1, 0, 1, 'Unidad', 'TIC'],
            ['Estiva de Madera', '', '', 1, 0, 1, '', ''],
            ['Hoja de Segueta', '', '', 1, 0, 1, '', ''],
            ['Escoba Insdustrial', '', '', 8, 4, 4, '', ''],
            ['Tornillo de teja 12 x 2 1/2"', 'Herramienta de construcción', '', 400, 0, 400, 'Unidad', ''],
        ];
    }
}
