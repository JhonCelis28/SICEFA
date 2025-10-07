<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Labor
 * @brief Modelo Eloquent para la tabla `labors` del módulo INFRASTOCK.
 *
 * Representa una labor o actividad específica. Contiene detalles sobre la actividad
 * asociada, la persona responsable, fechas de planificación y ejecución, descripción,
 * precio, estado, observaciones y destino. Utiliza SoftDeletes para un borrado lógico.
 */
class Labor extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'labors';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'activity_id',
        'person_id',
        'planning_date',
        'execution_date',
        'description',
        'price',
        'status',
        'observations',
        'destination',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    // Si existen otras relaciones (ej. con Activity, Person), se pueden añadir aquí con sus respectivos docblocks.
}
