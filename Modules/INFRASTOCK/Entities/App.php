<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @class App
 * @brief Modelo Eloquent para la tabla `apps` (implícita) del módulo INFRASTOCK.
 *
 * Este modelo representa una aplicación dentro de la estructura general del sistema.
 * Es un modelo básico que puede ser extendido para incluir más propiedades y relaciones
 * si el módulo INFRASTOCK necesita interactuar directamente con la entidad 'App'.
 * Utiliza la factory de Laravel para la creación de datos de prueba.
 */
class App extends Model
{
    use HasFactory;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo. (Por defecto, 'apps').
     */
    // protected $table = 'apps';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     * Actualmente vacío, pero se pueden añadir atributos si la tabla 'apps' tiene campos que necesitan ser llenados masivamente.
     */
    protected $fillable = [];
    
    /**
     * Crea una nueva instancia de la factory asociada a este modelo.
     * @return \Modules\INFRASTOCK\Database\factories\AppFactory
     */
    protected static function newFactory()
    {
        return \Modules\INFRASTOCK\Database\factories\AppFactory::new();
    }
}
