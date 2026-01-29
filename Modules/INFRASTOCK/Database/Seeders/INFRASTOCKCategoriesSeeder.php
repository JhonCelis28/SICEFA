<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\INFRASTOCK\Entities\InfrastockCategory;

/**
 * @class INFRASTOCKCategoriesSeeder
 * @brief Seeder para crear las categorías de insumos del módulo INFRASTOCK.
 *
 * Este seeder crea todas las categorías necesarias para clasificar los insumos
 * dentro del sistema INFRASTOCK.
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
            'Accesorio de plomería',
            'Accesorio de fijación',
            'Herramienta de corte',
            'Material eléctrico',
            'Herramienta de pintura',
            'Accesorio de seguridad',
            'Material de construcción',
            'Material de pintura',
            'Herramienta abrasiva',
            'Herramienta de medición',
            'Herramienta de construcción',
            'Material sanitario',
            'Material de limpieza',
            'Accesorio de jardinería',
            'Material de jardinería',
            'Accesorio de hogar',
            'Herramienta de jardinería',
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
    }
}
