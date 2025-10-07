<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class ProductiveUnit
 * @brief Modelo Eloquent para la tabla `productive_units` del módulo INFRASTOCK.
 *
 * Representa una unidad productiva o área dentro del centro de formación.
 * Contiene información como el nombre, descripción, ícono y las relaciones con
 * la persona responsable, el sector y la finca a la que pertenece.
 * Utiliza SoftDeletes para un borrado lógico.
 */
class ProductiveUnit extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'productive_units';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'person_id',
        'sector_id',
        'farm_id',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    /**
     * Define la relación de pertenencia a una persona (responsable).
     * Una unidad productiva pertenece a una `Person` (si se implementa).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    // public function person() { return $this->belongsTo(Person::class); }

    /**
     * Define la relación de pertenencia a un sector.
     * Una unidad productiva pertenece a un `Sector` (si se implementa).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    // public function sector() { return $this->belongsTo(Sector::class); }

    /**
     * Define la relación de pertenencia a una finca/granja.
     * Una unidad productiva pertenece a una `Farm` (si se implementa).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    // public function farm() { return $this->belongsTo(Farm::class); }
}
