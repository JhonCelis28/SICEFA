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
 * Inventario actualizado de bodega - Febrero 2026.
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

        // Array de insumos: [nombre, categoria, caracteristicas, cantidad_inicial, consumos, cantidad_restante, unidad_medida, observaciones]
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
     * Inventario actualizado de bodega - Febrero 2026.
     * Formato: [nombre, categoria, caracteristicas, cantidad_inicial, consumos, cantidad_restante, unidad_medida, observaciones]
     * 
     * @return array
     */
    private function getSuppliesData(): array
    {
        return [
            // === ITEM 1-10 ===
            ['Curva EMT de 3/4 de acero galvanizado', 'Material eléctrico', 'Curva EMT 90 grados, en acero galvanizado, para tubería EMT de 3/4 pulgada', 10, 0, 10, 'Unidad', ''],
            ['Adaptador macho PVC de 1-1/2 (blanco)', 'Tubería PVC', 'Adaptador macho material PVC, diámetro de 1-1/2 pulgadas, presión agua potable, color blanco', 30, 0, 30, 'Unidad', ''],
            ['Unión de PVC presión de 2 (blanco)', 'Tubería PVC', 'Unión PVC presión, diámetro de 2 pulgadas, color blanco', 25, 0, 25, 'Unidad', ''],
            ['Adaptador hembra PVC de 2 (blanco)', 'Tubería PVC', 'Adaptador hembra material PVC, diámetro de 2 pulgadas, presión agua potable, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Buje soldado 2x1 presión PVC', 'Tubería PVC', 'Buje soldado PVC, reducción de 2 a 1 pulgada, presión', 5, 0, 5, 'Unidad', ''],
            ['Adaptador macho PVC de 2 (blanco)', 'Tubería PVC', 'Adaptador macho material PVC, diámetro de 2 pulgadas, presión agua potable, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Alambre dulce calibre 16', 'Ferretería', 'Para amarre de hierro, 418 Kg por rollo', 12, 0, 12, 'Rollo', ''],
            ['Tubo de PVC presión 21-200 PSI 1/2 x 6 metros (blanco)', 'Tubería PVC', 'Tubo PVC presión 21-200 PSI, diámetro 1/2 pulgada, longitud 6 metros, color blanco', 19, 0, 19, 'Unidad', ''],
            ['Tubo conduit PVC SCH40 3/4 x 3 metros (gris)', 'Tubería PVC', 'Tubo conduit PVC SCH40, diámetro 3/4 pulgada, longitud 3 metros, color gris', 62, 0, 62, 'Unidad', ''],
            ['Varilla de acero 3/8 corrugada G-60', 'Ferretería', 'Varilla de acero corrugada G-60, diámetro 3/8 pulgada, longitud 6 metros', 409, 0, 409, 'Kilos', ''],

            // === ITEM 11-20 ===
            ['Unión universal lisa PVC de 1', 'Tubería PVC', 'Unión universal lisa PVC, diámetro 1 pulgada', 10, 0, 10, 'Unidad', ''],
            ['Alambre liso de acero galvanizado calibre 12', 'Ferretería', 'Alambre liso de acero galvanizado, calibre 12', 600, 0, 600, 'Kilos', ''],
            ['Brocha de 2 pulgadas', 'Ferretería', 'Brocha de diámetro 2 pulgadas, material cerda natural, cabo de plástico', 2, 0, 2, 'Unidad', ''],
            ['Tubo de PVC presión 21-200 PSI 3/4 x 6 metros (blanco)', 'Tubería PVC', 'Tubo PVC presión 21-200 PSI, diámetro 3/4 pulgada, longitud 6 metros, color blanco', 5, 0, 5, 'Unidad', ''],
            ['Brocha de 3 pulgadas', 'Ferretería', 'Brocha de diámetro 3 pulgadas, material cerda natural, cabo de plástico', 9, 0, 9, 'Unidad', ''],
            ['Plafón o roseta E27 de 4-1/2', 'Ferretería', 'Elaborado en cerámica con bornes para conexión en cobre (110/250 voltios, 600 W)', 10, 0, 10, 'Unidad', ''],
            ['Tubo EMT de 3/4 x 3 m acero galvanizado', 'Material eléctrico', 'Protección de cables eléctricos, acero galvanizado', 10, 0, 10, 'Unidad', ''],
            ['Breaker monopolar de 20 A', 'Material eléctrico', 'Breaker monopolar, capacidad 20 amperios', 12, 0, 12, 'Unidad', ''],
            ['Polín de madera cuadrado 4x4 cm x 3 metros', 'Ferretería', 'Polín de madera cuadrado 4x4 cm, 3 metros de largo', 20, 0, 20, 'Unidad', ''],
            ['Cinta teflón 10 m 1/2 tipo industrial', 'Ferretería', 'Cinta teflón, 10 metros, 1/2 pulgada, trabajo pesado tipo industrial', 18, 0, 18, 'Unidad', ''],

            // === ITEM 21-30 ===
            ['Adaptador hembra PVC de 1-1/2 (blanco)', 'Tubería PVC', 'Adaptador hembra material PVC, diámetro de 1-1/2 pulgadas, presión agua potable, color blanco', 25, 0, 25, 'Unidad', ''],
            ['Adaptador hembra PVC de 1-1/4 (blanco)', 'Tubería PVC', 'Adaptador hembra material PVC, diámetro de 1-1/4 pulgadas, presión agua potable, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Caja de paso 10x10 cm PVC hermética', 'Tubería PVC', 'Caja de paso PVC hermética, dimensiones 10x10 cm', 6, 0, 6, 'Unidad', ''],
            ['Adaptador macho PVC de 1-1/4 (blanco)', 'Tubería PVC', 'Adaptador macho material PVC, diámetro de 1-1/4 pulgadas, presión agua potable, color blanco', 23, 0, 23, 'Unidad', ''],
            ['Curva conduit de 90° PVC de 3/4', 'Tubería PVC', 'Curva conduit PVC, 90 grados, diámetro 3/4 pulgada', 11, 0, 11, 'Unidad', ''],
            ['Uniones sanitarias de 4 PVC', 'Tubería sanitaria', 'Unión sanitaria PVC, diámetro 4 pulgadas', 5, 0, 5, 'Unidad', ''],
            ['Bombillo LED A60 rosca E27 110V luz blanca', 'Luces LED', 'Bombillo LED A60, rosca E27, 110V, luz blanca, temperatura de color 6500K', 40, 0, 40, 'Unidad', ''],
            ['Disco de corte de metal 4.5 x 3/64 x 7/8', 'Ferretería', 'Disco de corte para metal, dimensiones 4.5" x 3/64" x 7/8"', 92, 0, 92, 'Unidad', ''],
            ['Adaptador macho PVC de 1/2 (blanco)', 'Tubería PVC', 'Adaptador macho material PVC, diámetro de 1/2 pulgada, presión agua potable, color blanco', 15, 0, 15, 'Unidad', ''],
            ['Adaptador hembra PVC de 1/2 (blanco)', 'Tubería PVC', 'Adaptador hembra material PVC, diámetro de 1/2 pulgada, presión agua potable, color blanco', 12, 0, 12, 'Unidad', ''],

            // === ITEM 31-40 ===
            ['Tornillo de acero 2-1/2 autoperforante con arandela (gris)', 'Ferretería', 'Tornillo de acero de 2-1/2 pulgadas, autoperforante con arandela, color gris', 500, 0, 500, 'Unidad', ''],
            ['Semicodo de PVC de 1/2 (blanco)', 'Tubería PVC', 'Semicodo PVC, diámetro 1/2 pulgada, color blanco', 79, 0, 79, 'Unidad', ''],
            ['Terminal EMT 3/4 de acero galvanizado', 'Material eléctrico', 'Terminal EMT para tubería de 3/4 pulgada, acero galvanizado', 20, 0, 20, 'Unidad', ''],
            ['Unión EMT de acero galvanizado de 3/4', 'Material eléctrico', 'Unión EMT para tubería de 3/4 pulgada, acero galvanizado', 46, 0, 46, 'Unidad', ''],
            ['Estribos o flejes 3/8 dimensión 15x25 cm', 'Ferretería', 'Estribos o flejes de 3/8 pulgada, dimensiones 15cm x 25cm', 1980, 0, 1980, 'Unidad', ''],
            ['Abrazadera EMT de 3/4 doble oreja', 'Material eléctrico', 'Abrazadera EMT de 3/4, doble oreja, paquete por 10 unidades', 2, 0, 2, 'Paquete', ''],
            ['Registro para ducha medio de paso cruceta', 'Tubería ducha', 'Registro para ducha de medio paso, tipo cruceta', 10, 0, 10, 'Unidad', ''],
            ['Tubo sanitario de PVC de 4 x 6 m tipo pesado', 'Tubería sanitaria', 'Tubo sanitario PVC, diámetro 4 pulgadas, longitud 6 metros, tipo pesado', 5, 0, 5, 'Unidad', ''],
            ['Candado intemperie encauchetado 52 mm', 'Ferretería', 'Candado para intemperie, encauchetado, 52 mm', 0, 0, 0, 'Unidad', ''],
            ['Grifos plásticos', 'Tubería', 'Grifos plásticos para instalación en tubería', 5, 0, 5, 'Unidad', ''],

            // === ITEM 41-50 ===
            ['Panel LED 60x60 cm 48W 110V 6500K', 'Luces LED', 'Panel LED de 60x60 cm, luminarias de cielo raso, flujo de luz uniforme, 48W, 110V, 6500K, 3700 lúmenes', 8, 0, 8, 'Unidad', ''],
            ['Pintura blanca exterior látex tipo 1 galón', 'Pinturas', 'Pintura blanca para exterior a base de látex tipo 1, galón x 18924 ml', 16, 0, 16, 'Galón', ''],
            ['Cerradura chapa derecha', 'Ferretería', 'Cerradura chapa derecha, caja y cantonera en acero, 2 llaves en latón', 2, 0, 2, 'Unidad', ''],
            ['Cerradura chapa izquierda', 'Ferretería', 'Cerradura chapa izquierda, caja y cantonera en acero, 2 llaves en latón', 2, 0, 2, 'Unidad', ''],
            ['Llave individual para lavamanos cromada tipo cruceta', 'Tubería lavamano', 'Llave individual para lavamanos cromada con maneral de tipo cruceta', 18, 0, 18, 'Unidad', ''],
            ['Breaker de riel bifásico de 40A', 'Material eléctrico', 'Breaker de riel bifásico, capacidad 40 amperios', 15, 0, 15, 'Unidad', 'Falta 1 unidad'],
            ['Soldadura PVC 1/4 de galón', 'Tubería PVC', 'Soldadura para PVC, presentación de 1/4 de galón', 8, 0, 8, 'Unidad', ''],
            ['Tubo sanitario de PVC de 3 x 6 m tipo pesado', 'Tubería sanitaria', 'Tubo sanitario PVC, diámetro 3 pulgadas, longitud 6 metros, tipo pesado', 3, 0, 3, 'Unidad', ''],
            ['Tubo PVC 1-1/4 x 6 m presión 21-200 PSI (blanco)', 'Tubería PVC', 'Tubo PVC presión 21-200 PSI, diámetro 1-1/4 pulgada, longitud 6 metros, color blanco', 5, 0, 5, 'Unidad', ''],
            ['Pintura verde pino a base de aceite galón', 'Pinturas', 'Pintura verde pino a base de aceite, presentación por galón', 20, 0, 20, 'Galón', ''],

            // === ITEM 51-60 ===
            ['Perfil metálico acero galvanizado calibre 18', 'Ferretería', 'Perfil metálico en acero galvanizado, calibre 18, dimensiones 6 x 0.8 x 0.4 m', 24, 0, 24, 'Unidad', ''],
            ['Cinta asfáltica tapa gotera impermeabilizante', 'Ferretería', 'Cinta asfáltica tapa gotera impermeabilizante, 15cm x 10m por rollo', 8, 0, 8, 'Rollo', ''],
            ['Grifería lavaplatos para pared plástico', 'Llave lavaplato', 'Grifería lavaplatos para instalación en la pared, material plástico', 20, 0, 20, 'Unidad', ''],
            ['Panel LED 30x120 cm 48W 110V 6500K', 'Luces LED', 'Panel LED de 30x120 cm, luminarias de cielo raso, luz uniforme, 48W, 110V, 6500K', 10, 0, 10, 'Unidad', ''],
            ['Cubierta master verde 1000 1x6 m calibre 28', 'Ferretería', 'Cubierta master color verde, 1000, dimensiones 1x6 m, calibre 28', 50, 0, 50, 'Unidad', ''],
            ['Breaker enchufable tripolar 40 A', 'Material eléctrico', 'Breaker enchufable tripolar, capacidad 40 amperios', 6, 0, 6, 'Unidad', ''],
            ['Pintura color caoba para madera fungicida', 'Pinturas', 'Pintura color caoba para madera, fungicida', 5, 0, 5, 'Galón', ''],
            ['Carretilla metálica buggy 3100', 'Ferretería', 'Carretilla metálica tipo buggy 3100', 0, 0, 0, 'Unidad', ''],
            ['Registro de ducha', 'Tubería', 'Registro de ducha estándar', 5, 0, 5, 'Unidad', ''],
            ['Pintura color beige epóxica alto desempeño', 'Pinturas', 'Pintura color beige, epóxica de alto desempeño', 10, 0, 10, 'Galón', ''],

            // === ITEM 61-70 ===
            ['Sellador elástico construcción base silicona', 'Ferretería', 'Sellador elástico para construcción a base de silicona', 8, 0, 8, 'Unidad', ''],
            ['Unión de PVC lisa de 1-1/4 presión', 'Tubería PVC', 'Unión PVC lisa, diámetro 1-1/4 pulgada, presión', 5, 0, 5, 'Unidad', ''],
            ['Clavija trifásica de caucho 50 Amp 250V', 'Material eléctrico', 'Clavija trifásica de caucho, 50 amperios, 250 voltios', 10, 0, 10, 'Unidad', ''],
            ['Baldosa cerámica para pared blanca 30x20 cm', 'Ferretería', 'Baldosa cerámica para pared, color blanco, dimensiones 30x20 cm', 6, 0, 6, 'Caja', ''],
            ['Unión de PVC lisa de 1-1/2 (blanco)', 'Tubería PVC', 'Unión PVC lisa, diámetro 1-1/2 pulgadas, color blanco', 25, 0, 25, 'Unidad', ''],
            ['Collar de derivación con inserto metálico 2x1/2', 'Ferretería', 'Collar de derivación con inserto metálico, medida 2 x 1/2 pulgada', 10, 0, 10, 'Unidad', ''],
            ['Toma trifásica de incrustar 3x50 Amp 250V', 'Material eléctrico', 'Toma trifásica de incrustar, 3x50 amperios, 250 voltios', 10, 0, 10, 'Unidad', ''],
            ['Válvula de bola terminal de 1/2 cierre rápido', 'Tubería', 'Válvula de bola terminal de 1/2 pulgada, de cierre rápido y alta presión', 20, 0, 20, 'Unidad', ''],
            ['Ángulo metálico 6m x 1 x 1/8', 'Ferretería', 'Ángulo metálico, longitud 6 metros, dimensiones 1 x 1/8 pulgada', 10, 0, 10, 'Unidad', ''],
            ['Tomacorriente doble blanco GFCI 15 Amp polo a tierra', 'Material eléctrico', 'Tomacorriente doble, color blanco, GFCI, 15 amperios, polo a tierra', 20, 0, 20, 'Unidad', 'Faltan 3 unidades'],

            // === ITEM 71-80 ===
            ['Unión universal lisa PVC de 1-1/2', 'Tubería PVC', 'Unión universal lisa PVC, diámetro 1-1/2 pulgadas', 8, 0, 8, 'Unidad', ''],
            ['Válvula de bola 2 agua metal', 'Tubería', 'Válvula de bola de 2 pulgadas para agua, material metal', 9, 0, 9, 'Unidad', ''],
            ['Manguera de nivel 1/4 x 40 m PVC transparente', 'Ferretería', 'Manguera de nivel, diámetro 1/4 pulgada, longitud 40 metros, PVC transparente', 1, 0, 1, 'Unidad', ''],
            ['Tubo PVC de 1-1/2 x 6 m presión (blanco)', 'Tubería PVC', 'Tubo PVC presión, diámetro 1-1/2 pulgadas, longitud 6 metros, color blanco', 5, 0, 5, 'Unidad', ''],
            ['Pegacor blanco saco x 25 kilos', 'Ferretería', 'Pegacor blanco, presentación saco de 25 kilos', 2, 0, 2, 'Bulto', ''],
            ['Candado 220-51MMB gancho acero negro', 'Ferretería', 'Candado 220-51MMB, gancho en acero, color negro', 0, 0, 0, 'Unidad', ''],
            ['Disolvente Xilol industrial botella 3 litros', 'Ferretería y pintura', 'Disolvente Xilol industrial, presentación botella de 3 litros', 2, 0, 2, 'Unidad', ''],
            ['Breaker enchufable bifásico 30 Amp', 'Material eléctrico', 'Breaker enchufable bifásico, capacidad 30 amperios', 10, 0, 10, 'Unidad', ''],
            ['Válvula bola 1/2 de metal', 'Tubería', 'Válvula de bola de 1/2 pulgada, material metal', 5, 0, 5, 'Unidad', ''],
            ['Tubo PVC 1 x 6 m presión (blanco)', 'Tubería PVC', 'Tubo PVC presión, diámetro 1 pulgada, longitud 6 metros, color blanco', 5, 0, 5, 'Unidad', ''],

            // === ITEM 81-90 ===
            ['Cemento gris de uso general bulto x 50 kg', 'Ferretería', 'Cemento gris de uso general, presentación bulto de 50 kg', 52, 0, 52, 'Bulto', ''],
            ['Breaker de riel trifásico de 40 Amp', 'Material eléctrico', 'Breaker de riel trifásico, capacidad 40 amperios', 4, 0, 4, 'Unidad', ''],
            ['Breaker de riel monopolar 40 Amp', 'Material eléctrico', 'Breaker de riel monopolar, capacidad 40 amperios', 15, 0, 15, 'Unidad', ''],
            ['Varilla de acero 1/2 corrugada G-60', 'Ferretería', 'Varilla de acero corrugada G-60, diámetro 1/2 pulgada', 1410, 0, 1410, 'Unidad', ''],
            ['Grapas para grapadora neumática 10mm', 'Grapas', 'Grapas para grapadora neumática, tamaño 10mm', 2, 0, 2, 'Caja', ''],
            ['Amarras plásticas negras 3.6x300mm', 'Ferretería', 'Amarras plásticas color negro, dimensiones 3.6x300mm, paquete por 100 unidades', 3, 0, 3, 'Paquete', ''],
            ['Toma doble 15 Amp 125V polo a tierra naranja', 'Material eléctrico', 'Toma doble, 15 amperios, 125 voltios, polo a tierra, color naranja', 20, 0, 20, 'Unidad', ''],
            ['Breaker industrial trifásico 80 Amp', 'Material eléctrico', 'Breaker industrial trifásico, capacidad 80 amperios', 2, 0, 2, 'Unidad', ''],
            ['Cable de cobre libre de halógeno rojo x 100 m', 'Ferretería', 'Cable de cobre libre de halógeno, color rojo, rollo de 100 metros', 1, 0, 1, 'Rollo', ''],
            ['Cable de cobre libre de halógeno blanco', 'Ferretería', 'Cable de cobre libre de halógeno, color blanco', 1, 0, 1, 'Rollo', ''],

            // === ITEM 91-100 ===
            ['Cable de cobre libre de halógeno verde', 'Ferretería', 'Cable de cobre libre de halógeno, color verde', 1, 0, 1, 'Rollo', ''],
            ['Malla eslabonada de alambre galvanizado', 'Ferretería', 'Malla eslabonada de alambre galvanizado', 9, 0, 9, 'Rollo', 'Faltan 2 rollos'],
            ['Válvula manual tipo push sanitario', 'Tubería sanitaria', 'Válvula manual tipo push para sanitario', 20, 0, 20, 'Unidad', ''],
            ['Cable eléctrico extensión trifásica encauchetado', 'Material eléctrico', 'Cable eléctrico extensión trifásica, encauchetado', 2, 0, 2, 'Unidad', ''],
            ['Tubo de PVC alcantarillado 16-400mm x 6m', 'Tubería PVC', 'Tubo PVC para alcantarillado, 16 pulgadas, 400mm, longitud 6 metros', 2, 0, 2, 'Unidad', ''],
            ['Pliego de lija', 'Ferretería', 'Pliego de lija para trabajo general', 3, 0, 3, 'Pliego', ''],
            ['Tornillos para drywall', 'Ferretería', 'Tornillos para drywall', 0, 0, 0, 'Unidad', ''],
            ['Contactor', 'Material eléctrico', 'Contactor eléctrico', 2, 0, 2, 'Unidad', ''],
            ['Capacitor', 'Material eléctrico', 'Capacitor eléctrico', 1, 0, 1, 'Unidad', ''],
            ['Termostato', 'Material eléctrico', 'Termostato eléctrico', 4, 0, 4, 'Unidad', ''],

            // === ITEM 110-120 ===
            ['Interruptores dobles', 'Material eléctrico', 'Interruptores dobles para instalación eléctrica', 5, 0, 5, 'Unidad', ''],
            ['Cabina trifásica', 'Material eléctrico', 'Cabina trifásica para distribución eléctrica', 1, 0, 1, 'Unidad', ''],
            ['Plomada de punto', 'Ferretería', 'Plomada de punto para nivelación', 3, 0, 3, 'Unidad', ''],
            ['Cinta para techo', 'Ferretería', 'Cinta para techo', 1, 0, 1, 'Rollo', ''],
            ['Cinta para drywall', 'Ferretería', 'Cinta para drywall en rollo', 5, 0, 5, 'Rollo', ''],
            ['Válvula pie 1', 'Tubería', 'Válvula de pie de 1 pulgada', 5, 0, 5, 'Unidad', ''],
            ['Válvula pie 1/2', 'Tubería', 'Válvula de pie de 1/2 pulgada', 5, 0, 5, 'Unidad', ''],
            ['Cheque de 1-1/4', 'Tubería', 'Cheque de 1-1/4 pulgadas', 5, 0, 5, 'Unidad', ''],
            ['Cheque de 2', 'Tubería', 'Cheque de 2 pulgadas', 2, 0, 2, 'Unidad', ''],
            ['Cheque de 1', 'Tubería', 'Cheque de 1 pulgada', 1, 0, 1, 'Unidad', ''],
            ['Codo de PVC de 3/4 (blanco)', 'Tubería PVC', 'Codo PVC, diámetro 3/4 pulgada, color blanco', 23, 0, 23, 'Unidad', ''],

            // === ITEM 121-130 ===
            ['Tapón roscado de PVC de 1/2 (blanco)', 'Tubería PVC', 'Tapón roscado PVC, diámetro 1/2 pulgada, color blanco', 20, 0, 20, 'Unidad', ''],
            ['Codo de PVC de 1/2 (blanco)', 'Tubería PVC', 'Codo PVC, diámetro 1/2 pulgada, color blanco', 23, 0, 23, 'Unidad', ''],
            ['Adaptador macho de PVC de 3/4 (blanco)', 'Tubería PVC', 'Adaptador macho PVC, diámetro 3/4 pulgada, color blanco', 102, 0, 102, 'Unidad', ''],
            ['Unión de reparación de 1', 'Tubería PVC', 'Unión de reparación PVC, diámetro 1 pulgada', 7, 0, 7, 'Unidad', ''],
            ['Adaptador hembra de PVC de 3/4 (blanco)', 'Tubería PVC', 'Adaptador hembra PVC, diámetro 3/4 pulgada, color blanco', 91, 0, 91, 'Unidad', ''],
            ['Nylon', 'Ferretería', 'Nylon en rollo', 2, 0, 2, 'Rollo', ''],
            ['Tapón liso de PVC de 1/2 (blanco)', 'Tubería PVC', 'Tapón liso PVC, diámetro 1/2 pulgada, color blanco', 8, 0, 8, 'Unidad', ''],
            ['PF macho de PVC de 1/2', 'Tubería PVC', 'Adaptador PF macho PVC, diámetro 1/2 pulgada', 104, 0, 104, 'Unidad', ''],
            ['Adaptador hembra de PVC de 1/2 (blanco)', 'Tubería PVC', 'Adaptador hembra PVC, diámetro 1/2 pulgada, color blanco', 16, 0, 16, 'Unidad', ''],
            ['Adaptador macho de PVC de 1 (blanco)', 'Tubería PVC', 'Adaptador macho PVC, diámetro 1 pulgada, color blanco', 38, 0, 38, 'Unidad', ''],

            // === ITEM 131-140 ===
            ['Codo de PVC de 2 (blanco)', 'Tubería PVC', 'Codo PVC, diámetro 2 pulgadas, color blanco', 27, 0, 27, 'Unidad', ''],
            ['Semicodo de PVC de 2', 'Tubería PVC', 'Semicodo PVC, diámetro 2 pulgadas', 19, 0, 19, 'Unidad', ''],
            ['Codo de PVC de 1-1/2', 'Tubería PVC', 'Codo PVC, diámetro 1-1/2 pulgadas', 6, 0, 6, 'Unidad', ''],
            ['Semicodo de PVC de 1', 'Tubería PVC', 'Semicodo PVC, diámetro 1 pulgada', 3, 0, 3, 'Unidad', ''],
            ['Codo sanitario de PVC de 2', 'PVC sanitaria', 'Codo sanitario PVC, diámetro 2 pulgadas', 9, 0, 9, 'Unidad', ''],
            ['Manguera multiuso de lavamanos', 'Tubería PVC', 'Manguera multiuso para lavamanos', 1, 0, 1, 'Unidad', ''],
            ['Brazos metálicos para lámpara', 'Material eléctrico', 'Brazos metálicos para instalación de lámpara', 2, 0, 2, 'Unidad', ''],
            ['Lámparas de posta', 'Material eléctrico', 'Lámparas de posta', 13, 0, 13, 'Unidad', ''],
            ['PF hembra de PVC de 1/2', 'Tubería PVC', 'Adaptador PF hembra PVC, diámetro 1/2 pulgada', 90, 0, 90, 'Unidad', ''],
            ['Push de ducha', 'Tubería', 'Push de ducha', 14, 0, 14, 'Unidad', ''],

            // === ITEM 141-150 ===
            ['Tee de PVC de 1/2 (blanco)', 'Tubería PVC', 'Tee PVC, diámetro 1/2 pulgada, color blanco', 131, 0, 131, 'Unidad', ''],
            ['Bisagras 3x4', 'Ferretería', 'Bisagras de 3x4 pulgadas', 12, 0, 12, 'Unidad', ''],
            ['Ganchos para colgar trapero', 'Ferretería', 'Ganchos para colgar trapero', 13, 0, 13, 'Unidad', ''],
            ['Disco para pulir', 'Ferretería', 'Disco para pulir', 9, 0, 9, 'Unidad', ''],
            ['Unión lisa de 1/2 (blanco)', 'Tubería PVC', 'Unión lisa PVC, diámetro 1/2 pulgada, color blanco', 44, 0, 44, 'Unidad', ''],
            ['Tapón liso de PVC de 1 (blanco)', 'Tubería PVC', 'Tapón liso PVC, diámetro 1 pulgada, color blanco', 4, 0, 4, 'Unidad', ''],
            ['Tapón roscado de PVC de 1 (blanco)', 'Tubería PVC', 'Tapón roscado PVC, diámetro 1 pulgada, color blanco', 12, 0, 12, 'Unidad', ''],
            ['Tee de PVC de 2 (blanco)', 'Tubería PVC', 'Tee PVC, diámetro 2 pulgadas, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Unión lisa de 3/4 (blanco)', 'Tubería PVC', 'Unión lisa PVC, diámetro 3/4 pulgada, color blanco', 9, 0, 9, 'Unidad', ''],

            // === ITEM 151-160 ===
            ['Adaptador hembra de PVC de 1 (blanco)', 'Tubería PVC', 'Adaptador hembra PVC, diámetro 1 pulgada, color blanco', 80, 0, 80, 'Unidad', ''],
            ['Unión lisa de PVC de 1-1/2 (blanco)', 'Tubería PVC', 'Unión lisa PVC, diámetro 1-1/2 pulgadas, color blanco', 2, 0, 2, 'Unidad', ''],
            ['Unión lisa de PVC de 1/4 (blanco)', 'Tubería PVC', 'Unión lisa PVC, diámetro 1/4 pulgada, color blanco', 62, 0, 62, 'Unidad', ''],
            ['Unión de PVC roscado de 1 (blanco)', 'Tubería PVC', 'Unión PVC roscada, diámetro 1 pulgada, color blanco', 16, 0, 16, 'Unidad', ''],
            ['Unión PVC lisa de 1 (blanco)', 'Tubería PVC', 'Unión PVC lisa, diámetro 1 pulgada, color blanco', 2, 0, 2, 'Unidad', ''],
            ['Tapón liso de PVC de 1-1/2 (blanco)', 'Tubería PVC', 'Tapón liso PVC, diámetro 1-1/2 pulgadas, color blanco', 11, 0, 11, 'Unidad', ''],
            ['Tapón liso de PVC con cuello de 1-1/2 (blanco)', 'Tubería PVC', 'Tapón liso PVC con cuello, diámetro 1-1/2 pulgadas, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Tapón liso de PVC de 2 (blanco)', 'Tubería PVC', 'Tapón liso PVC, diámetro 2 pulgadas, color blanco', 8, 0, 8, 'Unidad', ''],
            ['Semicodo de PVC de 1-1/2 (blanco)', 'Tubería PVC', 'Semicodo PVC, diámetro 1-1/2 pulgadas, color blanco', 11, 0, 11, 'Unidad', ''],
            ['Adaptador macho de PVC de 1 con empaque (blanco)', 'Tubería PVC', 'Adaptador macho PVC, diámetro 1 pulgada, con empaque, color blanco', 16, 0, 16, 'Unidad', ''],

            // === ITEM 161-170 ===
            ['Chapa paquete', 'Ferretería', 'Chapa en paquete', 10, 0, 10, 'Paquete', ''],
            ['Grata de copa 5/8', 'Ferretería', 'Grata de copa de 5/8 pulgada', 6, 0, 6, 'Unidad', ''],
            ['Boquilla', 'Ferretería', 'Boquilla en cajas', 8, 0, 8, 'Caja', ''],
            ['Lámparas de 18 W', 'Material eléctrico', 'Lámparas de 18 vatios', 1, 0, 1, 'Unidad', ''],
            ['Pata de mesa roscada', 'Ferretería', 'Pata de mesa roscada', 1, 0, 1, 'Unidad', ''],
            ['Codo de PVC de 1 (blanco)', 'Tubería PVC', 'Codo PVC, diámetro 1 pulgada, color blanco', 17, 0, 17, 'Unidad', ''],
            ['Semicodo de PVC de 3/4 (blanco)', 'Tubería PVC', 'Semicodo PVC, diámetro 3/4 pulgada, color blanco', 20, 0, 20, 'Unidad', ''],
            ['Unión lisa de PVC de 1-1/4 (blanco)', 'Tubería PVC', 'Unión lisa PVC, diámetro 1-1/4 pulgadas, color blanco', 5, 0, 5, 'Unidad', ''],

            // === ITEM 171-180 ===
            ['Bujes sanitarios de 6 a 4', 'Sanitario', 'Bujes sanitarios, reducción de 6 a 4 pulgadas', 2, 0, 2, 'Unidad', ''],
            ['Acople de PVC a manguera', 'PVC', 'Acople de PVC a manguera', 21, 0, 21, 'Unidad', ''],
            ['Collarines de 2 a 1/2', 'PVC', 'Collarines PVC, de 2 a 1/2 pulgada', 5, 0, 5, 'Unidad', ''],
            ['Tornillos para zinc de 2-1/2', 'Ferretería', 'Tornillos para zinc de 2-1/2 pulgadas', 130, 0, 130, 'Unidad', ''],
            ['Tornillos para zinc de 1-1/2', 'Ferretería', 'Tornillos para zinc de 1-1/2 pulgadas', 40, 0, 40, 'Unidad', ''],
            ['Manguera para lavamanos', 'Tubería PVC', 'Manguera para lavamanos', 4, 0, 4, 'Unidad', ''],
            ['Totalizadores', 'Material eléctrico', 'Totalizadores eléctricos', 2, 0, 2, 'Unidad', ''],
            ['Breaker bifásico de 20 A', 'Material eléctrico', 'Breaker bifásico, capacidad 20 amperios', 5, 0, 5, 'Unidad', ''],
            ['Reducción de PVC de 1 a 3/4 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1 a 3/4 pulgada, color blanco', 133, 0, 133, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 3/4 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 3/4 pulgada, color blanco', 16, 0, 16, 'Unidad', ''],

            // === ITEM 181-190 ===
            ['Reducción de PVC de 1-1/4 a 1 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1-1/4 a 1 pulgada, color blanco', 39, 0, 39, 'Unidad', ''],
            ['Reducción de PVC de 1 a 1/4 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1 a 1/4 pulgada, color blanco', 6, 0, 6, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 2 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 2 pulgadas, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 1-1/4 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 1-1/4 pulgadas, color blanco', 13, 0, 13, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 1/2 (blanca)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 1/2 pulgada, color blanco', 2, 0, 2, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 1/2 (negra)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 1/2 pulgada, color negro', 2, 0, 2, 'Unidad', ''],
            ['Reducción de PVC de 2 a 1 (blanco)', 'Tubería PVC', 'Reducción PVC, de 2 a 1 pulgada, color blanco', 5, 0, 5, 'Unidad', ''],
            ['Reducción de PVC de 1-1/2 a 1 (blanco)', 'Tubería PVC', 'Reducción PVC, de 1-1/2 a 1 pulgada, color blanco', 2, 0, 2, 'Unidad', ''],
            ['Sifón sanitario PVC de 3 (amarillo)', 'PVC sanitaria', 'Sifón sanitario PVC, diámetro 3 pulgadas, color amarillo', 4, 0, 4, 'Unidad', ''],

            // === ITEM 191-200 ===
            ['Sifón sanitario PVC de 2 (amarillo)', 'PVC sanitaria', 'Sifón sanitario PVC, diámetro 2 pulgadas, color amarillo', 6, 0, 6, 'Unidad', ''],
            ['Unión sanitaria PVC de 2 (amarillo)', 'PVC sanitaria', 'Unión sanitaria PVC, diámetro 2 pulgadas, color amarillo', 15, 0, 15, 'Unidad', ''],
            ['Unión sanitaria PVC de 3 (amarillo)', 'PVC sanitaria', 'Unión sanitaria PVC, diámetro 3 pulgadas, color amarillo', 11, 0, 11, 'Unidad', ''],
            ['Unión sanitaria PVC de 1-1/2 (amarillo)', 'PVC sanitaria', 'Unión sanitaria PVC, diámetro 1-1/2 pulgadas, color amarillo', 1, 0, 1, 'Unidad', ''],
            ['Codo sanitario de PVC de 1-1/2 (amarillo)', 'PVC sanitaria', 'Codo sanitario PVC, diámetro 1-1/2 pulgadas, color amarillo', 24, 0, 24, 'Unidad', ''],
            ['Reducción de PVC de 2 a 3/4 (blanco)', 'Tubería PVC', 'Reducción PVC, de 2 a 3/4 pulgada, color blanco', 1, 0, 1, 'Unidad', ''],
            ['Reducción de PVC de 2 a 1/2 (blanco)', 'Tubería PVC', 'Reducción PVC, de 2 a 1/2 pulgada, color blanco', 1, 0, 1, 'Unidad', ''],
            ['Tee de PVC de 1-1/4 (blanco)', 'Tubería PVC', 'Tee PVC, diámetro 1-1/4 pulgadas, color blanco', 21, 0, 21, 'Unidad', ''],
            ['Reducción sanitario PVC de 3 a 2 (amarillo)', 'PVC sanitaria', 'Reducción sanitario PVC, de 3 a 2 pulgadas, color amarillo', 7, 0, 7, 'Unidad', ''],
            ['Reducción sanitario PVC de 4 a 2 (amarillo)', 'PVC sanitaria', 'Reducción sanitario PVC, de 4 a 2 pulgadas, color amarillo', 6, 0, 6, 'Unidad', ''],

            // === ITEM 201-210 ===
            ['Reducción sanitario PVC de 2 a 1-1/2 (amarillo)', 'PVC sanitaria', 'Reducción sanitario PVC, de 2 a 1-1/2 pulgadas, color amarillo', 8, 0, 8, 'Unidad', ''],
            ['Puntillas', 'Ferretería', 'Puntillas en cajas', 0, 0, 0, 'Caja', ''],
            ['Luces LED de 60 W', 'Material eléctrico', 'Luces LED de 60 vatios, en caja', 4, 0, 4, 'Caja', 'Pendiente -2'],
            ['Unión universal de PVC de 1-1/4 (blanco)', 'Tubería PVC', 'Unión universal PVC, diámetro 1-1/4 pulgadas, color blanco', 15, 0, 15, 'Unidad', ''],
            ['Llaves de paso de PVC de 2 (blanco)', 'Tubería PVC', 'Llaves de paso PVC, diámetro 2 pulgadas, color blanco', 10, 0, 10, 'Unidad', ''],
            ['Cuchillas de guadaña', 'Ferretería', 'Cuchillas para guadaña', 4, 0, 4, 'Unidad', ''],
            ['Sensores eléctricos', 'Inventario Ingeniería', 'Sensores eléctricos en caja', 4, 0, 4, 'Caja', ''],
            ['Guardamotores', 'Inventario Ingeniería', 'Guardamotores', 12, 0, 12, 'Unidad', ''],
            ['Contactores', 'Inventario Ingeniería', 'Contactores', 10, 0, 10, 'Unidad', ''],
            ['Flotador con válvulas completo', 'Tubería', 'Flotador con sus respectivas válvulas, completo', 4, 0, 4, 'Unidad', ''],

            // === ITEM 211-220 ===
            ['Flotador para tanque solo bomba', 'Tubería', 'Flotador RTAN para tanque, solo la bomba', 1, 0, 1, 'Unidad', ''],
            ['Presostatos', 'Ferretería', 'Presostatos', 4, 0, 4, 'Unidad', ''],
            ['Orinal de empotrar tipo push', 'Sanitario', 'Orinal de empotrar, tipo push', 2, 0, 2, 'Unidad', ''],
            ['Cerradura chapa de bola', 'Ferretería', 'Cerradura (chapa) de bola', 6, 0, 6, 'Unidad', ''],
            ['Llave terminal para jardín de 1/2', 'Tubería', 'Llave terminal para jardín de 1/2 pulgada', 8, 0, 8, 'Unidad', ''],
            ['Cinta aislante negra', 'Ferretería', 'Cinta aislante color negro', 8, 0, 8, 'Rollo', ''],
            ['Cajas rawelt', 'Material eléctrico', 'Cajas rawelt', 3, 0, 3, 'Unidad', ''],
            ['Tomacorrientes', 'Material eléctrico', 'Tomacorrientes', 2, 0, 2, 'Unidad', ''],
            ['Tapas gris para intemperie', 'Material eléctrico', 'Tapas color gris para intemperie', 3, 0, 3, 'Unidad', ''],
            ['Brocas de 3/8 de tungsteno', 'Ferretería', 'Brocas de 3/8 pulgada, material tungsteno', 2, 0, 2, 'Unidad', ''],

            // === ITEM 221-226 ===
            ['Broca de 3/8 para metal', 'Ferretería', 'Broca de 3/8 pulgada para metal', 1, 0, 1, 'Unidad', ''],
            ['Broca de 1/4 para concreto', 'Ferretería', 'Broca de 1/4 pulgada para concreto', 2, 0, 2, 'Unidad', ''],
            ['Soldadura 60-13', 'Ferretería', 'Soldadura 60-13', 1, 0, 1, 'Kilos', ''],
            ['Soldadura 60-11', 'Ferretería', 'Soldadura 60-11', 1, 0, 1, 'Kilos', 'Pendiente -3 soldaduras'],
            ['Puntas taladro', 'Ferretería', 'Puntas para taladro', 6, 0, 6, 'Unidad', ''],
            ['Brocha de 1/2 pulgada', 'Ferretería', 'Brocha de 1/2 pulgada', 1, 0, 1, 'Unidad', ''],
        ];
    }
}
