<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;

/**
 * @class INFRASTOCKToolsSeeder
 * @brief Seeder para crear las herramientas iniciales del módulo INFRASTOCK.
 *
 * Este seeder crea todas las herramientas con sus características, cantidades
 * y estados correspondientes.
 * Inventario actualizado de bodega - Febrero 2026.
 */
class INFRASTOCKToolsSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     * Crea todas las herramientas con sus datos correspondientes.
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

        // Obtener la categoría de herramientas
        $category = InfrastockCategory::where('type', 'tool')->where('name', 'Herramienta')->first();

        // Array de herramientas: [nombre, cantidad_total, cantidad_disponible, estado, observaciones]
        $tools = $this->getToolsData();

        $created = 0;
        $updated = 0;

        foreach ($tools as $tool) {
            $nombre = trim($tool[0]);
            $cantidadTotal = (int) $tool[1];
            $cantidadDisponible = (int) $tool[2];
            $estado = trim($tool[3]);
            $descripcion = trim($tool[4] ?? '');

            $record = Tool::updateOrCreate(
                [
                    'nombre' => $nombre,
                    'labor_id' => $defaultLabor->id,
                    'inventory_id' => $defaultInventory->id,
                ],
                [
                    'nombre' => $nombre,
                    'category_id' => $category ? $category->id : null,
                    'descripcion' => $descripcion ?: null,
                    'estado' => $estado,
                    'cantidad_total' => $cantidadTotal,
                    'cantidad_disponible' => $cantidadDisponible,
                    'amount' => $cantidadTotal,
                    'price' => 0,
                    'labor_id' => $defaultLabor->id,
                    'inventory_id' => $defaultInventory->id,
                ]
            );

            if ($record->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Herramientas procesadas: {$created} creadas, {$updated} actualizadas.");
    }

    /**
     * Retorna el array de datos de herramientas.
     * Inventario actualizado de bodega - Febrero 2026.
     * Formato: [nombre, cantidad_total, cantidad_disponible, estado, descripcion]
     *
     * @return array
     */
    private function getToolsData(): array
    {
        return [
            // [nombre, cantidad_total, cantidad_disponible, estado, descripcion]
            ['Juego de llaves bristol milimétricas y pulgadas de 30 piezas', 3, 3, 'disponible', 'Juego de llaves bristol milimétricas y pulgadas, 30 piezas por juego'],
            ['Juego de brocas y puntas para taladro concreto', 2, 2, 'disponible', 'Juego de brocas y puntas para taladro de concreto'],
            ['Rodillos para pintar', 9, 9, 'disponible', 'Rodillos para pintar'],
            ['Balastras', 4, 4, 'disponible', 'Balastras'],
            ['Zaranda', 1, 1, 'disponible', 'Zaranda'],
            ['Cortaseto', 1, 1, 'disponible', 'Cortaseto'],
            ['Sopladora de espalda', 2, 1, 'disponible', 'Sopladora de espalda. Total 2 unidades, 1 disponible'],
            ['Guadaña', 2, 1, 'disponible', 'Guadaña. Total 2 unidades, 1 disponible'],
            ['Pala', 1, 1, 'disponible', 'Pala'],
            ['Maseta de hierro', 1, 1, 'disponible', 'Maseta de hierro'],
            ['Barra de hierro', 1, 1, 'disponible', 'Barra de hierro'],
            ['Maseta de caucho', 1, 1, 'disponible', 'Maseta de caucho'],
            ['Tijeras de jardinería', 6, 6, 'disponible', 'Tijeras de jardinería'],
            ['Palustres', 5, 5, 'disponible', 'Palustres'],
            ['Escuadra', 3, 3, 'disponible', 'Escuadra'],
            ['Punteros (cincel)', 3, 3, 'disponible', 'Punteros tipo cincel'],
            ['Cortatubos', 2, 2, 'disponible', 'Cortatubos'],
            ['Cortabaldosa', 1, 0, 'mantenimiento', 'Cortabaldosa. En mantenimiento'],
            ['Soldador', 1, 1, 'disponible', 'Soldador'],
            ['Lima', 4, 4, 'disponible', 'Lima'],
            ['Pulidora', 3, 2, 'disponible', 'Pulidora. Total 3 unidades, 2 disponibles'],
            ['Motosierra', 1, 1, 'disponible', 'Motosierra'],
            ['Cargador automático 24V-10AMP', 1, 1, 'disponible', 'Cargador automático 24V-10AMP'],
            ['Espátula', 2, 2, 'disponible', 'Espátula'],
            ['Juego de llaves', 1, 1, 'disponible', 'Juego de llaves'],
            ['Escalera amarilla (tijera)', 2, 2, 'disponible', 'Escalera tipo tijera, color amarillo'],
            ['Escalera roja (tijera)', 2, 2, 'disponible', 'Escalera tipo tijera, color rojo'],
            ['Escalera desplegable (amarilla)', 1, 1, 'disponible', 'Escalera desplegable, color amarillo'],
        ];
    }
}
