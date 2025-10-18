# Sistema de Personal de Aseo - INFRASTOCK

## Objetivo Implementado

Crear un sistema completo para el Personal de Aseo con la misma funcionalidad de iniciar sesión que el administrador, pero con su propio dashboard específico y todas las funciones necesarias.

## 🔧 **Implementación Realizada**

### **1. Página de Bienvenida Modificada**

#### **Archivo**: `Modules/INFRASTOCK/Resources/views/index.blade.php`

#### **Funcionalidad Implementada:**
- ✅ **Detección de roles**: El sistema detecta si el usuario tiene rol "Aseo"
- ✅ **Botón específico**: Muestra "Personal de Aseo" para usuarios con rol Aseo
- ✅ **Botón administrador**: Muestra "Administrador" para otros usuarios
- ✅ **Redirección inteligente**: Los botones de login incluyen parámetro de redirección

#### **Código Implementado:**

##### **A. Navegación Desktop:**
```blade
@guest
    <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" 
       href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}">
        Inicia sesión
    </a>
@else
    @php
        $userRoles = Auth::user()->roles->pluck('name')->toArray();
    @endphp
    @if(in_array('Aseo', $userRoles))
        <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" 
           href="{{ route('infrastock.cleaning-staff.dashboard') }}">
            Personal de Aseo
        </a>
    @else
        <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" 
           href="{{ route('cefa.infrastock.admin.dashboard') }}">
            Administrador
        </a>
    @endif
@endguest
```

##### **B. Navegación Móvil:**
```blade
@guest
    <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" 
       href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}">
        Inicia sesión
    </a>
@else
    @php
        $userRoles = Auth::user()->roles->pluck('name')->toArray();
    @endphp
    @if(in_array('Aseo', $userRoles))
        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" 
           href="{{ route('infrastock.cleaning-staff.dashboard') }}">
            Personal de Aseo
        </a>
    @else
        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" 
           href="{{ route('cefa.infrastock.admin.dashboard') }}">
            Administrador
        </a>
    @endif
@endguest
```

### **2. Sistema de Redirección Post-Login**

#### **Archivo**: `Modules/INFRASTOCK/Http/Controllers/INFRASTOCKController.php`

#### **Método Implementado:**
```php
/**
 * Maneja la lógica posterior al inicio de sesión para el módulo INFRASTOCK.
 * Redirige al usuario autenticado al dashboard correspondiente según su rol.
 * @return \Illuminate\Http\RedirectResponse
 */
public function postlogin(){
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login')->with('error', 'Usuario no autenticado');
    }

    // Obtener los roles del usuario
    $userRoles = $user->roles->pluck('name')->toArray();
    
    // Verificar el rol del usuario y redirigir al dashboard correspondiente
    if (in_array('Aseo', $userRoles)) {
        return redirect()->route('infrastock.cleaning-staff.dashboard');
    } else {
        // Para administradores o usuarios sin rol específico
        return redirect()->route('cefa.infrastock.admin.dashboard');
    }
}
```

### **3. Ruta de Redirección**

#### **Archivo**: `Modules/INFRASTOCK/Routes/web.php`

#### **Ruta Agregada:**
```php
// Ruta para manejar la redirección después del login desde SICA
Route::get('/infrastock/post-login', 'INFRASTOCKController@postlogin')->name('infrastock.post-login');
```

## 🏗️ **Dashboard del Personal de Aseo**

### **Funcionalidades Implementadas:**

#### **1. Estadísticas en Tiempo Real:**
- ✅ **Solicitudes Pendientes**: Contador de solicitudes en estado "Solicitud"
- ✅ **Solicitudes Aprobadas**: Contador de solicitudes aprobadas
- ✅ **Solicitudes Entregadas**: Contador de solicitudes entregadas
- ✅ **Solicitudes Rechazadas**: Contador de solicitudes rechazadas

#### **2. Acciones Rápidas:**
- ✅ **Nueva Solicitud**: Crear solicitud de insumos
- ✅ **Notificaciones**: Ver notificaciones de estado
- ✅ **Mi Perfil**: Gestionar perfil del usuario
- ✅ **Historial**: Visualizar historial de insumos

#### **3. Información del Usuario:**
- ✅ **Nombre del usuario**: Mostrado en el header
- ✅ **Email del usuario**: Mostrado en el header
- ✅ **Menú de usuario**: Acceso a perfil y logout

#### **4. Notificaciones Recientes:**
- ✅ **Lista de notificaciones**: Últimas notificaciones
- ✅ **Estados visuales**: Iconos para aprobado, rechazado, entregado
- ✅ **Enlace a todas**: Botón para ver todas las notificaciones

#### **5. Solicitudes Recientes:**
- ✅ **Tabla de solicitudes**: Últimas solicitudes del usuario
- ✅ **Estados visuales**: Badges de colores para cada estado
- ✅ **Enlace a detalles**: Acceso a información completa

### **Rutas Disponibles:**

#### **Dashboard Principal:**
- `GET /infrastock/cleaning-staff/dashboard` → Dashboard principal

#### **Gestión de Solicitudes:**
- `GET /infrastock/cleaning-staff/requests` → Mis solicitudes
- `GET /infrastock/cleaning-staff/requests/create` → Crear solicitud
- `POST /infrastock/cleaning-staff/requests` → Guardar solicitud

#### **Notificaciones:**
- `GET /infrastock/cleaning-staff/notifications` → Notificaciones

#### **Perfil de Usuario:**
- `GET /infrastock/cleaning-staff/profile` → Mi perfil
- `PUT /infrastock/cleaning-staff/profile` → Actualizar perfil

#### **Reportes:**
- `GET /infrastock/cleaning-staff/surplus-report` → Reporte de sobrantes
- `GET /infrastock/cleaning-staff/supply-history` → Historial de insumos

#### **Autenticación:**
- `GET /infrastock/cleaning-staff/login` → Formulario de login
- `POST /infrastock/cleaning-staff/login` → Procesar login
- `POST /infrastock/cleaning-staff/logout` → Cerrar sesión
- `GET /infrastock/cleaning-staff/register` → Formulario de registro
- `POST /infrastock/cleaning-staff/register` → Procesar registro

## 🎯 **Flujo de Funcionamiento**

### **1. Usuario No Autenticado:**
```
1. Visita /infrastock
2. Ve página de bienvenida con botón "Inicia sesión"
3. Hace clic en "Inicia sesión"
4. Es redirigido a SICA login con parámetro redirect_to
5. Ingresa credenciales en SICA
6. SICA lo redirige de vuelta a /infrastock/post-login
7. El método postlogin() detecta su rol
8. Si tiene rol "Aseo" → Dashboard Personal de Aseo
9. Si no tiene rol "Aseo" → Dashboard Administrador
```

### **2. Usuario Autenticado con Rol "Aseo":**
```
1. Visita /infrastock
2. Ve página de bienvenida con botón "Personal de Aseo"
3. Hace clic en "Personal de Aseo"
4. Es redirigido directamente al dashboard del Personal de Aseo
5. Ve estadísticas, acciones rápidas, notificaciones y solicitudes
```

### **3. Usuario Autenticado sin Rol "Aseo":**
```
1. Visita /infrastock
2. Ve página de bienvenida con botón "Administrador"
3. Hace clic en "Administrador"
4. Es redirigido al dashboard de administrador
```

## 🔐 **Seguridad y Autenticación**

### **1. Middleware de Autenticación:**
- ✅ **Rutas protegidas**: Todas las rutas del dashboard requieren autenticación
- ✅ **Verificación de roles**: Se verifica que el usuario tenga el rol correcto
- ✅ **Redirección segura**: URLs de redirección son validadas

### **2. Gestión de Sesiones:**
- ✅ **Logout funcional**: Botón de cerrar sesión en el dashboard
- ✅ **Persistencia de sesión**: Mantiene la sesión entre módulos
- ✅ **Redirección post-logout**: Regresa a la página de bienvenida

## 📱 **Responsive Design**

### **1. Adaptabilidad:**
- ✅ **Desktop**: Navegación horizontal con botón específico según rol
- ✅ **Mobile**: Menú colapsable con botón específico según rol
- ✅ **Tablet**: Adaptación automática del layout

### **2. Experiencia de Usuario:**
- ✅ **Transiciones suaves**: Animaciones CSS para mejor UX
- ✅ **Iconos descriptivos**: Font Awesome para claridad visual
- ✅ **Colores consistentes**: Paleta de colores verde de INFRASTOCK

## 🧪 **Pruebas de Funcionamiento**

### **Para Probar el Sistema:**

#### **1. Usuario No Autenticado:**
1. Visita `/infrastock`
2. Haz clic en "Inicia sesión"
3. Ingresa credenciales de un usuario con rol "Aseo"
4. Deberías ser redirigido al dashboard del Personal de Aseo

#### **2. Usuario Autenticado con Rol "Aseo":**
1. Visita `/infrastock` (ya autenticado)
2. Deberías ver el botón "Personal de Aseo"
3. Haz clic en el botón para ir al dashboard

#### **3. Usuario Autenticado sin Rol "Aseo":**
1. Visita `/infrastock` (ya autenticado)
2. Deberías ver el botón "Administrador"
3. Haz clic en el botón para ir al dashboard de administrador

#### **4. Verificar Funcionalidades del Dashboard:**
1. **Estadísticas**: Deberían mostrar números reales
2. **Acciones Rápidas**: Todos los botones deberían funcionar
3. **Notificaciones**: Deberían aparecer si hay notificaciones
4. **Solicitudes**: Deberían aparecer las solicitudes del usuario

## ✅ **Estado Final**

- ✅ **Página de bienvenida modificada**: Detecta rol y muestra botón específico
- ✅ **Sistema de redirección implementado**: Post-login redirige según rol
- ✅ **Dashboard del Personal de Aseo funcional**: Con todas las funcionalidades
- ✅ **Autenticación integrada**: Funciona con el sistema de SICA
- ✅ **Detección de roles**: Redirige al dashboard correcto según el rol
- ✅ **Responsive design**: Funciona en desktop y mobile
- ✅ **Seguridad implementada**: Rutas protegidas y validación de roles
- ✅ **Todas las rutas funcionando**: Dashboard completo con todas las funciones

## 🚀 **Funcionalidades del Dashboard**

### **1. Estadísticas:**
- Solicitudes pendientes, aprobadas, entregadas y rechazadas
- Enlaces directos a secciones relacionadas

### **2. Acciones Rápidas:**
- Nueva solicitud de insumos
- Ver notificaciones
- Gestionar perfil
- Ver historial de insumos

### **3. Notificaciones Recientes:**
- Últimas notificaciones con iconos de estado
- Enlace para ver todas las notificaciones

### **4. Solicitudes Recientes:**
- Tabla con las últimas solicitudes
- Estados visuales con colores
- Enlace para ver detalles

**¡El sistema del Personal de Aseo está completamente implementado y funcional!**
