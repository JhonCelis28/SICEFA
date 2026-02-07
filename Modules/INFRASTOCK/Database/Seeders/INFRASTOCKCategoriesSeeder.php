<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\INFRASTOCK\Entities\InfrastockCategory;

/**
 * @class INFRASTOCKCategoriesSeeder
 * @brief Seeder para crear las categorías de insumos y herramientas del módulo INFRASTOCK.
 *
 * Este seeder crea todas las categorías necesarias para clasificar los insumos
 * y herramientas dentro del sistema INFRASTOCK.
 */
class INFRASTOCKCategoriesSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     * Crea todas las categorías de insumos necesarias.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            'Material eléctrico',
            'Tubería PVC',
            'Ferretería',
            'Tubería sanitaria',
            'Luces LED',
            'Pinturas',
            'Tubería',
            'Tubería lavamano',
            'Llave lavaplato',
            'Tubería ducha',
            'Grapas',
            'PVC sanitaria',
            'Sanitario',
            'PVC',
            'Inventario Ingeniería',
            'Ferretería y pintura',
        ];

        foreach ($categories as $categoryName) {
            InfrastockCategory::updateOrCreate(
                ['name' => $categoryName],
                [
                    'name' => $categoryName,
                    'type' => 'supply'
                ]
            );
        }

        $this->command->info('Categorías de insumos registradas/actualizadas correctamente.');

        // Categorías para herramientas (type = 'tool')
        $toolCategories = [
            'Herramienta',
        ];

        foreach ($toolCategories as $categoryName) {
            InfrastockCategory::updateOrCreate(
                ['name' => $categoryName, 'type' => 'tool'],
                [
                    'name' => $categoryName,
                    'type' => 'tool'
                ]
            );
        }

        $this->command->info('Categorías de herramientas registradas/actualizadas correctamente.');
    }
}
