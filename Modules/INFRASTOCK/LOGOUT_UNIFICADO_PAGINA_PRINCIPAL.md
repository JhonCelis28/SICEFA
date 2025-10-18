# Logout Unificado - Redirección a Página Principal SICEFA

## 🎯 **Objetivo Implementado**

Configurar el logout tanto del Personal de Aseo como del Administrador para que redirijan directamente a la página principal de SICEFA donde están todos los módulos disponibles, proporcionando una experiencia consistente y unificada.

## 🔧 **Cambios Realizados**

### **1. Controlador Personal de Aseo**

#### **Archivo**: `Modules/INFRASTOCK/Http/Controllers/CleaningStaffController.php`

#### **Método**: `logout()`

**Actualizado:**
```php
public function logout(Request $request)
{
    auth()->logout();
    
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('cefa.welcome')
        ->with('success', 'Has cerrado sesión correctamente.');
}
```

### **2. Controlador Administrador**

#### **Archivo**: `Modules/INFRASTOCK/Http/Controllers/INFRASTOCKController.php`

#### **Método**: `logout()` - **NUEVO**

**Agregado:**
```php
/**
 * Cierra la sesión del usuario administrador.
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
```

### **3. Rutas Configuradas**

#### **Archivo**: `Modules/INFRASTOCK/Routes/web.php`

**Rutas agregadas:**
```php
Route::middleware(['web', 'auth'])->group(function () {
    // Logout del administrador
    Route::post('/infrastock/admin/logout', 'INFRASTOCKController@logout')->name('infrastock.admin.logout');
    
    // Logout del personal de aseo
    Route::post('/infrastock/cleaning-staff/logout', 'CleaningStaffController@logout')->name('infrastock.cleaning-staff.logout');
    
    // ... resto de rutas
});
```

### **4. Layout Administrador Actualizado**

#### **Archivo**: `Modules/INFRASTOCK/Resources/views/layouts/master.blade.php`

**Actualizado:**
```blade
<a href="{{ route('infrastock.admin.logout') }}" 
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    <i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión
</a>
<form id="logout-form" action="{{ route('infrastock.admin.logout') }}" method="POST" class="hidden">
    @csrf
</form>
```

## 🎨 **Características del Sistema Unificado**

### **1. Flujo de Logout Consistente:**

#### **A. Personal de Aseo:**
1. Usuario hace clic en "Cerrar Sesión"
2. Se ejecuta `CleaningStaffController@logout`
3. Se invalida la sesión
4. Se redirige a `cefa.welcome`
5. Usuario ve la página principal de SICEFA con todos los módulos

#### **B. Administrador:**
1. Usuario hace clic en "Cerrar Sesión"
2. Se ejecuta `INFRASTOCKController@logout`
3. Se invalida la sesión
4. Se redirige a `cefa.welcome`
5. Usuario ve la página principal de SICEFA con todos los módulos

### **2. Rutas Específicas:**

#### **A. Personal de Aseo:**
- **Ruta**: `POST /infrastock/cleaning-staff/logout`
- **Nombre**: `infrastock.cleaning-staff.logout`
- **Controlador**: `CleaningStaffController@logout`

#### **B. Administrador:**
- **Ruta**: `POST /infrastock/admin/logout`
- **Nombre**: `infrastock.admin.logout`
- **Controlador**: `INFRASTOCKController@logout`

### **3. Destino Común:**

#### **A. Página Principal SICEFA:**
- **Ruta**: `GET /`
- **Nombre**: `cefa.welcome`
- **Controlador**: `HomeController@welcome`
- **Contenido**: Todos los módulos disponibles del sistema

### **4. Seguridad Mantenida:**

#### **A. Invalidación de Sesión:**
- ✅ **auth()->logout()** - Cierra la sesión del usuario
- ✅ **session()->invalidate()** - Invalida la sesión actual
- ✅ **session()->regenerateToken()** - Regenera el token CSRF

#### **B. Mensaje de Confirmación:**
- ✅ **Mensaje de éxito** - "Has cerrado sesión correctamente."
- ✅ **Feedback visual** - Usuario sabe que el logout fue exitoso

## 🧪 **Para Probar el Sistema**

### **1. Logout Personal de Aseo:**
1. Accede al dashboard del personal de aseo
2. Haz clic en el menú desplegable del usuario
3. Selecciona "Cerrar Sesión"
4. Verifica que seas redirigido a la página principal de SICEFA
5. Confirma que veas todos los módulos disponibles

### **2. Logout Administrador:**
1. Accede al dashboard del administrador
2. Haz clic en el menú desplegable del usuario
3. Selecciona "Cerrar Sesión"
4. Verifica que seas redirigido a la página principal de SICEFA
5. Confirma que veas todos los módulos disponibles

### **3. Verificar Consistencia:**
1. Prueba el logout desde ambos roles
2. Confirma que ambos vayan al mismo destino
3. Verifica que aparezca el mensaje de éxito
4. Confirma que puedas acceder a cualquier módulo desde la página principal

## ✅ **Estado Final**

### **Implementado Completamente:**

1. **Logout Personal de Aseo**: Redirige a `cefa.welcome`
2. **Logout Administrador**: Redirige a `cefa.welcome`
3. **Rutas Específicas**: Cada rol tiene su propia ruta de logout
4. **Controladores Actualizados**: Ambos manejan el logout correctamente
5. **Layouts Actualizados**: Ambos usan las rutas específicas

### **Beneficios Logrados:**

- **Consistencia Total**: Ambos roles van al mismo destino
- **Experiencia Unificada**: Punto de salida común para todos
- **Flexibilidad**: Usuario puede acceder a cualquier módulo después del logout
- **Seguridad**: Invalidación correcta de sesiones
- **Mantenibilidad**: Código organizado y específico por rol

## 🔗 **Rutas Relacionadas:**

- **Logout Personal de Aseo**: `POST /infrastock/cleaning-staff/logout`
- **Logout Administrador**: `POST /infrastock/admin/logout`
- **Página Principal SICEFA**: `GET /` (ruta `cefa.welcome`)

## 📝 **Próximos Pasos:**

1. **Probar Ambos Logouts**: Verificar que funcionen correctamente
2. **Confirmar Redirección**: Asegurar que vayan a la página principal
3. **Verificar Mensajes**: Confirmar que aparezcan los mensajes de éxito
4. **Probar Navegación**: Desde la página principal a otros módulos

**¡El logout está completamente unificado y redirige a la página principal de SICEFA donde están todos los módulos!** 🎉

## 🔄 **Flujo Completo Unificado:**

1. **Usuario en cualquier Dashboard** → Personal de Aseo o Administrador
2. **Clic en "Cerrar Sesión"** → Menú desplegable
3. **Ejecución del Logout** → Controlador específico del rol
4. **Invalidación de Sesión** → Seguridad completa
5. **Redirección** → Página principal de SICEFA (`cefa.welcome`)
6. **Mensaje de Éxito** → "Has cerrado sesión correctamente."
7. **Acceso Universal** → Usuario puede acceder a cualquier módulo del sistema
8. **Experiencia Consistente** → Mismo comportamiento para todos los roles
