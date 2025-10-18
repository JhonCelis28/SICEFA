<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @class CleaningStaffController
 * @brief Controlador para el dashboard de Personal de Aseo en el módulo INFRASTOCK.
 *
 * Este controlador maneja todas las funcionalidades específicas para el personal de aseo:
 * - Registro de usuarios
 * - Dashboard principal con estadísticas
 * - Solicitud de insumos
 * - Notificaciones de estado de solicitud
 * - Gestión de perfil del usuario
 * - Generación de reportes de sobrantes (solo filtros)
 * - Visualización de historial de insumos
 */
class CleaningStaffController extends Controller
{
    /**
     * Muestra el formulario de registro para personal de aseo.
     * @return Renderable
     */
    public function showRegistrationForm()
    {
        return view('infrastock::cleaning-staff.register');
    }

    /**
     * Procesa el registro de nuevo personal de aseo.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'first_last_name' => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:20|unique:people,document_number',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_last_name.required' => 'El primer apellido es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Crear la persona
            $person = Person::create([
                'first_name' => $request->first_name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Crear el usuario
            $user = User::create([
                'person_id' => $person->id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nickname' => $request->first_name . ' ' . $request->first_last_name,
            ]);

            // Asignar rol de personal de aseo (si existe)
            $cleaningStaffRole = Role::where('name', 'Personal de Aseo')->first();
            if ($cleaningStaffRole) {
                $user->roles()->attach($cleaningStaffRole->id);
            }

            return redirect()->route('infrastock.cleaning-staff.login')
                ->with('success', 'Registro exitoso. Ya puedes iniciar sesión con tus credenciales.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el usuario. Inténtalo de nuevo.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Muestra el formulario de login para personal de aseo.
     * @return Renderable
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('infrastock.cleaning-staff.dashboard');
        }
        
        return view('infrastock::cleaning-staff.login');
    }

    /**
     * Procesa el login del personal de aseo.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (auth()->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('infrastock.cleaning-staff.dashboard'))
                ->with('success', '¡Bienvenido al sistema INFRASTOCK!');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'])
            ->withInput($request->except('password'));
    }

    /**
     * Cierra la sesión del usuario.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('cefa.welcome')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Muestra el dashboard principal del personal de aseo.
     * @return Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Obtener estadísticas del usuario actual
        $pendingRequests = WarehouseMovement::where('user_id', $user->id)
            ->where('role', 'Solicitud')
            ->where('item_type', 'equipment')
            ->count();

        $approvedRequests = WarehouseMovement::where('user_id', $user->id)
            ->where('role', 'approved')
            ->where('item_type', 'equipment')
            ->count();

        $deliveredRequests = WarehouseMovement::where('user_id', $user->id)
            ->where('role', 'delivered')
            ->where('item_type', 'equipment')
            ->count();

        $rejectedRequests = WarehouseMovement::where('user_id', $user->id)
            ->where('role', 'rejected')
            ->where('item_type', 'equipment')
            ->count();

        // Obtener solicitudes recientes del usuario
        $recentRequests = WarehouseMovement::with('equipment', 'productiveUnitWarehouse.productiveUnit')
            ->where('user_id', $user->id)
            ->where('item_type', 'equipment')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Obtener notificaciones (solicitudes con cambios de estado recientes)
        $notifications = WarehouseMovement::with('equipment')
            ->where('user_id', $user->id)
            ->where('item_type', 'equipment')
            ->whereIn('role', ['approved', 'rejected', 'delivered'])
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->get();

        // Obtener insumos disponibles para solicitar (usando el nuevo sistema de stock)
        $availableSupplies = Equipment::with('category')
            ->get()
            ->filter(function($equipment) {
                return $equipment->stock > 0;
            })
            ->sortBy('name');

        return view('infrastock::cleaning-staff.dashboard', compact(
            'pendingRequests',
            'approvedRequests', 
            'deliveredRequests',
            'rejectedRequests',
            'recentRequests',
            'notifications',
            'availableSupplies'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud de insumo.
     * @return Renderable
     */
    public function createRequest()
    {
        $equipments = Equipment::with('category')
            ->get()
            ->filter(function($equipment) {
                return $equipment->stock > 0;
            })
            ->sortBy('name');

        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->get();

        return view('infrastock::cleaning-staff.create-request', compact('equipments', 'productiveUnitWarehouses'));
    }

    /**
     * Almacena una nueva solicitud de insumo.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'movement_id' => 'required|exists:equipments,id',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        // Verificar que el insumo tenga cantidad suficiente
        $equipment = Equipment::findOrFail($request->movement_id);
        if (!$equipment->hasStockFor($request->amount)) {
            return redirect()->back()->with('error', 'No hay suficiente stock disponible para este insumo. Stock disponible: ' . $equipment->stock);
        }

        WarehouseMovement::create([
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'movement_id' => $request->movement_id,
            'item_type' => 'equipment',
            'user_id' => auth()->id(),
            'role' => 'Solicitud',
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('infrastock.cleaning-staff.requests.create')
            ->with('success', 'Solicitud de insumo creada exitosamente.');
    }

    /**
     * Muestra todas las solicitudes del usuario actual.
     * @return Renderable
     */
    public function myRequests()
    {
        $user = auth()->user();
        
        $requests = WarehouseMovement::with('equipment', 'productiveUnitWarehouse.productiveUnit', 'productiveUnitWarehouse.warehouse')
            ->where('user_id', $user->id)
            ->where('item_type', 'equipment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('infrastock::cleaning-staff.my-requests', compact('requests'));
    }

    /**
     * Muestra las notificaciones del usuario.
     * @return Renderable
     */
    public function notifications()
    {
        $user = auth()->user();
        
        $notifications = WarehouseMovement::with('equipment')
            ->where('user_id', $user->id)
            ->where('item_type', 'equipment')
            ->whereIn('role', ['approved', 'rejected', 'delivered'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('infrastock::cleaning-staff.notifications', compact('notifications'));
    }

    /**
     * Muestra el formulario de gestión de perfil.
     * @return Renderable
     */
    public function profile()
    {
        $user = auth()->user();
        return view('infrastock::cleaning-staff.profile', compact('user'));
    }

    /**
     * Actualiza el perfil del usuario.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Actualizar datos del usuario
        $user->update([
            'email' => $request->email,
        ]);

        // Actualizar datos de la persona
        if ($user->person) {
            $user->person->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        return redirect()->route('infrastock.cleaning-staff.profile')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Genera reporte de sobrantes (solo filtros, sin exportación).
     * @return Renderable
     */
    public function surplusReport()
    {
        $user = auth()->user();
        
        // Obtener insumos entregados al usuario que podrían tener sobrantes
        $deliveredSupplies = WarehouseMovement::with('equipment')
            ->where('user_id', $user->id)
            ->where('role', 'delivered')
            ->where('item_type', 'equipment')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas de uso
        $totalDelivered = $deliveredSupplies->sum('amount');
        $uniqueSupplies = $deliveredSupplies->groupBy('movement_id')->count();

        return view('infrastock::cleaning-staff.surplus-report', compact(
            'deliveredSupplies',
            'totalDelivered',
            'uniqueSupplies'
        ));
    }

    /**
     * Muestra el historial de insumos con disponibilidad.
     * @return Renderable
     */
    public function supplyHistory()
    {
        // Obtener todos los insumos con su historial de movimientos
        $supplies = Equipment::with(['category', 'warehouseMovements' => function($query) {
            $query->where('user_id', auth()->id())
                  ->orderBy('created_at', 'desc');
        }])
        ->orderBy('name')
        ->get();

        // Obtener estadísticas de disponibilidad
        $totalSupplies = $supplies->count();
        $availableSupplies = $supplies->filter(function($supply) {
            return $supply->stock > 0;
        })->count();
        $lowStockSupplies = $supplies->filter(function($supply) {
            return $supply->stock <= 5 && $supply->stock > 0;
        })->count();
        $outOfStockSupplies = $supplies->filter(function($supply) {
            return $supply->stock == 0;
        })->count();

        return view('infrastock::cleaning-staff.supply-history', compact(
            'supplies',
            'totalSupplies',
            'availableSupplies',
            'lowStockSupplies',
            'outOfStockSupplies'
        ));
    }
}
