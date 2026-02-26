<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Exports\ToolsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
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
    public function index(Request $request)
    {
        $query = Tool::with('category', 'labor', 'inventory');

        // Búsqueda server-side
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('placa', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('estado', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tools = $query->paginate(15)->appends($request->query());
        $categories = InfrastockCategory::where('type', 'tool')->get();
        $labors = Labor::all();
        $inventories = Inventory::all();
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
            'nombre' => 'required|string|max:255|unique:tools,nombre',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'placa' => 'required|string|max:255|unique:tools,placa',
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

            if ($request->filled('cantidad_disponible') && $request->filled('cantidad_total')) {
                if ((int) $request->cantidad_disponible > (int) $request->cantidad_total) {
                    $errorMsg = 'La cantidad disponible no puede ser mayor que la cantidad total.';
                    if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMsg], 422);
                    }
                    return redirect()->route('infrastock.admin.tools.index')->with('error', $errorMsg)->withInput();
                }
            }
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
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
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

        if ($request->filled('cantidad_disponible') && $request->filled('cantidad_total')) {
            if ((int) $request->cantidad_disponible > (int) $request->cantidad_total) {
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', 'La cantidad disponible no puede ser mayor que la cantidad total.')
                    ->withInput();
            }
        }

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

        // Verificar si la herramienta tiene un estado que impide su eliminación
        $estadosProtegidos = ['en_prestamo', 'mantenimiento'];
        if (in_array($tool->estado, $estadosProtegidos)) {
            $estadoLabels = [
                'en_prestamo' => 'En Préstamo',
                'mantenimiento' => 'Mantenimiento',
            ];
            $estadoLabel = $estadoLabels[$tool->estado] ?? $tool->estado;
            $message = "No se puede eliminar la herramienta porque se encuentra en estado: {$estadoLabel}.";

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('infrastock.admin.tools.index')->with('error', $message);
        }

        // Verificar si tiene préstamos activos (pendientes o aprobados) en warehouse_movements
        $activeLoans = WarehouseMovement::where('movement_id', $tool->id)
            ->where('role', 'Préstamo')
            ->whereIn('status', ['pending', 'approved'])
            ->whereNull('deleted_at')
            ->count();

        if ($activeLoans > 0) {
            $message = "No se puede eliminar la herramienta porque tiene {$activeLoans} préstamo(s) activo(s).";
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('infrastock.admin.tools.index')->with('error', $message);
        }

        // Verificar si tiene devoluciones pendientes de aprobación
        $pendingReturns = WarehouseMovement::where('movement_id', $tool->id)
            ->where('role', 'Devolución')
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->count();

        if ($pendingReturns > 0) {
            $message = "No se puede eliminar la herramienta porque tiene {$pendingReturns} devolución(es) pendiente(s) de aprobación.";
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('infrastock.admin.tools.index')->with('error', $message);
        }

        // Eliminar imagen si existe
        if ($tool->imagen && \Storage::disk('public')->exists($tool->imagen)) {
            \Storage::disk('public')->delete($tool->imagen);
        }

        $tool->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Herramienta eliminada exitosamente.']);
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'deleted');
    }

    /**
     * Exporta todas las herramientas a un archivo PDF.
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $tools = Tool::with('category', 'labor', 'inventory')
                    ->orderBy('nombre', 'asc')
                    ->get();

        $pdf = Pdf::loadView('infrastock::admin.tools.exports.pdf', [
            'tools' => $tools
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'debugKeepTemp' => false,
        ]);

        $filename = 'Inventario_Herramientas_INFRASTOCK_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Exporta todas las herramientas a un archivo Excel.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $tools = Tool::with('category', 'labor', 'inventory')
                    ->orderBy('nombre', 'asc')
                    ->get();

        $filename = 'Inventario_Herramientas_INFRASTOCK_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new ToolsExport($tools), $filename);
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
