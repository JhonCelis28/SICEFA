<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;

/**
 * @class ToolController
 * @brief Controlador para la gestión de Herramientas en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para las herramientas.
 * Permite registrar, visualizar, actualizar y eliminar herramientas.
 * Utiliza modales para la creación y edición de herramientas para una experiencia de usuario optimizada.
 */
class ToolController extends Controller
{
    /**
     * Muestra una lista de todas las herramientas registradas.
     * Carga las relaciones de categoría, labor e inventario para cada herramienta.
     * También obtiene categorías de tipo 'tool', labores e inventarios para poblar los selectores en los modales.
     * @return Renderable
     */
    public function index()
    {
        $tools = Tool::with('category', 'labor', 'inventory')->paginate(15); // Obtiene las herramientas con paginación (15 por página).
        $categories = InfrastockCategory::where('type', 'tool')->get(); // Obtiene categorías específicas para herramientas.
        $labors = Labor::all(); // Obtiene todas las labores.
        $inventories = Inventory::all(); // Obtiene todos los inventarios.
        return view('infrastock::admin.tools.index', compact('tools', 'categories', 'labors', 'inventories'));
    }

    /**
     * Muestra el formulario para crear una nueva herramienta.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.tools.index');
    }

    /**
     * Almacena una nueva herramienta en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos de la herramienta.
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'placa' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'descripcion_actual' => 'nullable|string',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'categoria_id' => 'nullable|exists:infrastock_categories,id',
            'category_id' => 'nullable|exists:infrastock_categories,id',
            'estado' => 'nullable|in:disponible,en_prestamo,mantenimiento,no_disponible',
            'cantidad_total' => 'nullable|integer|min:0',
            'cantidad_disponible' => 'nullable|integer|min:0',
            'fecha_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'fecha_adquisicion' => 'nullable|date',
            'atributos' => 'nullable|string',
            'descripcion_mantenimiento' => 'nullable|string',
            'inventory_id' => 'nullable|exists:inventories,id',
            'labor_id' => 'nullable|exists:labors,id',
            'amount' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Siempre devolver JSON si tiene el header X-Requested-With
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $data = $request->all();
            
            // Filtrar solo los campos que existen en la base de datos
            $filteredData = $this->filterExistingColumns($data);
            
            // Manejar categoria_id si se envía
            if (isset($data['categoria_id']) && !isset($filteredData['category_id'])) {
                $filteredData['category_id'] = $data['categoria_id'];
            }
            
            // Manejar la carga de imagen solo si la columna existe
            if ($request->hasFile('imagen') && Schema::hasColumn('tools', 'imagen')) {
                $imagen = $request->file('imagen');
                $imagenPath = $imagen->store('tools', 'public');
                $filteredData['imagen'] = $imagenPath;
            }
            
            Tool::create($filteredData);
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar error de entrada duplicada
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                // Siempre devolver JSON si tiene el header X-Requested-With
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe una herramienta con estos datos. Por favor, verifica la información.'
                    ], 422);
                }
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', 'Ya existe una herramienta con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            
            // Manejar error de campo requerido sin valor por defecto
            if (strpos($e->getMessage(), "doesn't have a default value") !== false) {
                $fieldName = '';
                if (strpos($e->getMessage(), 'labor_id') !== false) {
                    $fieldName = 'Labor';
                } elseif (strpos($e->getMessage(), 'inventory_id') !== false) {
                    $fieldName = 'Inventario';
                }
                
                $errorMessage = $fieldName 
                    ? "El campo '{$fieldName}' es requerido. Por favor, selecciona un valor."
                    : "Faltan campos requeridos. Por favor, completa todos los campos obligatorios.";
                
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', $errorMessage)
                    ->withInput();
            }
            
            throw $e;
        }
        
        // Siempre devolver JSON si tiene el header X-Requested-With
        if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Herramienta registrada exitosamente.'
            ]);
        }
        
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta registrada exitosamente.');
    }

    /**
     * Muestra los detalles de una herramienta específica.
     * @param int $id El ID de la herramienta.
     * @return Renderable
     */
    public function show($id)
    {
        $tool = Tool::with('category', 'labor', 'inventory')->findOrFail($id); // Encuentra la herramienta con sus relaciones.
        return view('infrastock::admin.tools.show', compact('tool')); // Retorna la vista show con los detalles de la herramienta.
    }

    /**
     * Muestra el formulario para editar una herramienta específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID de la herramienta.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.tools.index');
    }

    /**
     * Actualiza una herramienta existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados de la herramienta.
     * @param int $id El ID de la herramienta a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'placa' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'descripcion_actual' => 'nullable|string',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'categoria_id' => 'nullable|exists:infrastock_categories,id',
            'category_id' => 'nullable|exists:infrastock_categories,id',
            'estado' => 'nullable|in:disponible,en_prestamo,mantenimiento,no_disponible',
            'cantidad_total' => 'nullable|integer|min:0',
            'cantidad_disponible' => 'nullable|integer|min:0',
            'fecha_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date',
            'fecha_adquisicion' => 'nullable|date',
            'atributos' => 'nullable|string',
            'descripcion_mantenimiento' => 'nullable|string',
            'inventory_id' => 'nullable|exists:inventories,id',
            'labor_id' => 'nullable|exists:labors,id',
            'amount' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $tool = Tool::findOrFail($id);
        
        try {
            $data = $request->all();
            
            // Filtrar solo los campos que existen en la base de datos
            $filteredData = $this->filterExistingColumns($data);
            
            // Manejar categoria_id si se envía
            if (isset($data['categoria_id']) && !isset($filteredData['category_id'])) {
                $filteredData['category_id'] = $data['categoria_id'];
            }
            
            // Manejar la carga de imagen solo si la columna existe
            if ($request->hasFile('imagen') && Schema::hasColumn('tools', 'imagen')) {
                // Eliminar imagen anterior si existe
                if ($tool->imagen && \Storage::disk('public')->exists($tool->imagen)) {
                    \Storage::disk('public')->delete($tool->imagen);
                }
                
                $imagen = $request->file('imagen');
                $imagenPath = $imagen->store('tools', 'public');
                $filteredData['imagen'] = $imagenPath;
            }
            
            $tool->update($filteredData);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', 'Ya existe una herramienta con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta actualizada exitosamente.');
    }

    /**
     * Elimina una herramienta de la base de datos.
     * @param int $id El ID de la herramienta a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $tool = Tool::findOrFail($id);
        $hasRelatedRecords = false; // TODO: Implement validation
        
        if ($hasRelatedRecords) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la herramienta porque tiene registros relacionados.'
                ], 422);
            }
            return redirect()->route('infrastock.admin.tools.index')->with('error', 'No se puede eliminar la herramienta porque tiene registros relacionados.');
        }
        
        $tool->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Herramienta eliminada exitosamente.'
            ]);
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'deleted');
    }

    /**
     * Filtra los datos para incluir solo las columnas que existen en la tabla tools.
     * Maneja campos requeridos que pueden no estar presentes.
     * @param array $data Los datos a filtrar
     * @return array Los datos filtrados
     */
    private function filterExistingColumns(array $data)
    {
        $filteredData = [];
        $tableName = 'tools';
        
        // Obtener todas las columnas de la tabla
        $columns = Schema::getColumnListing($tableName);
        
        // Verificar si las columnas labor_id e inventory_id son nullable usando SQL
        $laborIdNullable = false;
        $inventoryIdNullable = false;
        
        try {
            $connection = Schema::getConnection();
            // Verificar labor_id
            $laborIdInfo = $connection->select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'labor_id'");
            if (!empty($laborIdInfo)) {
                $laborIdNullable = strtoupper($laborIdInfo[0]->Null) === 'YES';
            }
            // Verificar inventory_id
            $inventoryIdInfo = $connection->select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'inventory_id'");
            if (!empty($inventoryIdInfo)) {
                $inventoryIdNullable = strtoupper($inventoryIdInfo[0]->Null) === 'YES';
            }
        } catch (\Exception $e) {
            // Si no se puede obtener la información, asumir que no son nullable
        }
        
        // Filtrar solo los campos que existen en la tabla
        foreach ($data as $key => $value) {
            if (in_array($key, $columns)) {
                // Incluir el campo si tiene valor
                if ($value !== '' && $value !== null) {
                    $filteredData[$key] = $value;
                } elseif ($value === null || $value === '') {
                    // Solo incluir null/empty si la columna lo permite
                    if ($key === 'labor_id' && $laborIdNullable) {
                        $filteredData[$key] = null;
                    } elseif ($key === 'inventory_id' && $inventoryIdNullable) {
                        $filteredData[$key] = null;
                    } elseif (!in_array($key, ['labor_id', 'inventory_id', 'id', 'created_at', 'updated_at', 'deleted_at'])) {
                        // Para otros campos opcionales, permitir null
                        $filteredData[$key] = null;
                    }
                }
            }
        }
        
        // Manejar campos requeridos que pueden no estar en los datos
        // labor_id e inventory_id no se utilizan en el sistema de préstamos, siempre establecer como null si no están presentes
        if (!isset($filteredData['labor_id']) && in_array('labor_id', $columns)) {
            $filteredData['labor_id'] = null;
        }
        if (!isset($filteredData['inventory_id']) && in_array('inventory_id', $columns)) {
            $filteredData['inventory_id'] = null;
        }
        
        return $filteredData;
    }
}
