<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class InfrastockCategory
 * @brief Modelo Eloquent para la tabla `infrastock_categories` del módulo INFRASTOCK.
 *
 * Representa una categoría utilizada para clasificar tanto insumos (`Equipment`)
 * como herramientas (`Tool`). Incluye un tipo para diferenciar entre categorías
 * de insumos y de herramientas. Utiliza SoftDeletes para un borrado lógico.
 */
class InfrastockCategory extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'infrastock_categories';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    /**
     * Define la relación uno a muchos con los equipos/insumos.
     * Una categoría puede tener muchos `Equipment`.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    /**
     * Define la relación uno a muchos con las herramientas.
     * Una categoría puede tener muchas `Tool`.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tools()
    {
        return $this->hasMany(Tool::class, 'category_id');
    }
}
