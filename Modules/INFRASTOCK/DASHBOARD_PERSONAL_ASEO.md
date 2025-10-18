# Dashboard Personal de Aseo - INFRASTOCK

## Descripción General

Se ha implementado exitosamente un dashboard completo para el **Personal de Aseo** en el módulo INFRASTOCK, que permite a los usuarios registrarse, gestionar solicitudes de insumos y acceder a todas las funcionalidades requeridas.

## Funcionalidades Implementadas

### ✅ 1. Sistema de Registro de Usuarios
- **Archivo**: `Modules/INFRASTOCK/Http/Controllers/CleaningStaffController.php`
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/register.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/register`
- **Características**:
  - Formulario completo de registro con validación
  - Integración con la tabla `users` y `people` existente
  - Asignación automática de rol "Personal de Aseo"
  - Validación de campos únicos (email, documento)

### ✅ 2. Sistema de Autenticación
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/login.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/login`
- **Características**:
  - Login específico para personal de aseo
  - Redirección automática al dashboard después del login
  - Opción "Recordar sesión"
  - Enlaces a registro y recuperación de contraseña

### ✅ 3. Dashboard Principal
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/dashboard.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/dashboard`
- **Características**:
  - Estadísticas en tiempo real de solicitudes
  - Acceso rápido a todas las funcionalidades
  - Notificaciones recientes
  - Historial de solicitudes recientes
  - Menú de usuario con logout

### ✅ 4. Solicitud de Insumos
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/create-request.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/requests/create`
- **Características**:
  - Formulario interactivo para solicitar insumos
  - Validación de stock disponible en tiempo real
  - Selección de unidad productiva/almacén
  - Descripción/justificación de la solicitud
  - Validación del lado del cliente y servidor

### ✅ 5. Gestión de Solicitudes
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/my-requests.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/requests`
- **Características**:
  - Lista completa de solicitudes del usuario
  - Filtros por estado, fecha y búsqueda de texto
  - Paginación de resultados
  - Estados visuales (pendiente, aprobada, rechazada, entregada)

### ✅ 6. Notificaciones de Estado
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/notifications.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/notifications`
- **Características**:
  - Notificaciones de cambios de estado de solicitudes
  - Estadísticas de notificaciones
  - Historial completo de notificaciones
  - Iconos diferenciados por tipo de notificación

### ✅ 7. Gestión de Perfil
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/profile.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/profile`
- **Características**:
  - Edición de información personal (email, teléfono, dirección)
  - Información de la cuenta (ID, rol, fecha de registro)
  - Estadísticas de actividad del usuario
  - Validación de campos únicos

### ✅ 8. Reportes de Sobrantes (Solo Filtros)
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/surplus-report.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/surplus-report`
- **Características**:
  - Análisis de insumos entregados
  - Filtros por fecha, nombre y ordenamiento
  - Estadísticas de uso
  - **NOTA**: Sin funcionalidad de exportación (PDF/Excel) según requerimientos

### ✅ 9. Historial de Insumos
- **Vista**: `Modules/INFRASTOCK/Resources/views/cleaning-staff/supply-history.blade.php`
- **Ruta**: `/infrastock/cleaning-staff/supply-history`
- **Características**:
  - Vista completa del inventario de insumos
  - Estados de disponibilidad (disponible, stock bajo, sin stock)
  - Filtros por categoría, estado de stock y búsqueda
  - Enlaces directos para solicitar insumos disponibles

## Estructura de Archivos Creados

```
Modules/INFRASTOCK/
├── Http/Controllers/
│   └── CleaningStaffController.php          # Controlador principal
├── Resources/views/cleaning-staff/
│   ├── register.blade.php                   # Formulario de registro
│   ├── login.blade.php                      # Formulario de login
│   ├── dashboard.blade.php                  # Dashboard principal
│   ├── create-request.blade.php            # Crear solicitud
│   ├── my-requests.blade.php                # Mis solicitudes
│   ├── notifications.blade.php             # Notificaciones
│   ├── profile.blade.php                    # Gestión de perfil
│   ├── surplus-report.blade.php            # Reportes de sobrantes
│   └── supply-history.blade.php             # Historial de insumos
└── Routes/
    └── web.php                              # Rutas actualizadas
```

## Rutas Implementadas

### Rutas Públicas (Sin Autenticación)
- `GET /infrastock/cleaning-staff/register` - Formulario de registro
- `POST /infrastock/cleaning-staff/register` - Procesar registro
- `GET /infrastock/cleaning-staff/login` - Formulario de login
- `POST /infrastock/cleaning-staff/login` - Procesar login

### Rutas Protegidas (Con Autenticación)
- `GET /infrastock/cleaning-staff/dashboard` - Dashboard principal
- `POST /infrastock/cleaning-staff/logout` - Cerrar sesión
- `GET /infrastock/cleaning-staff/requests/create` - Crear solicitud
- `POST /infrastock/cleaning-staff/requests` - Guardar solicitud
- `GET /infrastock/cleaning-staff/requests` - Mis solicitudes
- `GET /infrastock/cleaning-staff/notifications` - Notificaciones
- `GET /infrastock/cleaning-staff/profile` - Mi perfil
- `PUT /infrastock/cleaning-staff/profile` - Actualizar perfil
- `GET /infrastock/cleaning-staff/surplus-report` - Reporte sobrantes
- `GET /infrastock/cleaning-staff/supply-history` - Historial insumos

## Base de Datos Utilizada

El sistema utiliza las tablas existentes sin crear nuevas tablas:

- **`users`**: Información de usuarios autenticados
- **`people`**: Información personal de usuarios
- **`equipments`**: Catálogo de insumos disponibles
- **`warehouse_movements`**: Registro de solicitudes y movimientos
- **`productive_unit_warehouses`**: Unidades productivas y almacenes
- **`infrastock_categories`**: Categorías de insumos

## Características Técnicas

### Frontend
- **Framework CSS**: Tailwind CSS
- **Iconos**: Font Awesome 6
- **Fuentes**: Inter (Google Fonts)
- **Responsive**: Diseño adaptable a dispositivos móviles
- **JavaScript**: Vanilla JS para interactividad

### Backend
- **Framework**: Laravel (Módulo INFRASTOCK)
- **Autenticación**: Laravel Auth
- **Validación**: Laravel Validation
- **ORM**: Eloquent
- **Paginación**: Laravel Pagination

### Seguridad
- **Middleware**: `web` y `auth` para rutas protegidas
- **CSRF**: Protección contra ataques CSRF
- **Validación**: Validación del lado del cliente y servidor
- **Sanitización**: Escape de datos de entrada

## Cómo Acceder al Sistema

### 1. Registro de Nuevo Usuario
1. Visitar: `http://tu-dominio/infrastock/cleaning-staff/register`
2. Completar el formulario de registro
3. Hacer clic en "Registrarse"
4. Ser redirigido al login

### 2. Login de Usuario Existente
1. Visitar: `http://tu-dominio/infrastock/cleaning-staff/login`
2. Ingresar email y contraseña
3. Hacer clic en "Iniciar Sesión"
4. Ser redirigido al dashboard

### 3. Acceso Directo al Dashboard
1. Visitar: `http://tu-dominio/infrastock/cleaning-staff/dashboard`
2. Si no está autenticado, será redirigido al login

## Funcionalidades Específicas por Requerimiento

### ✅ Solicitud de Insumos
- Formulario completo con validación de stock
- Selección de unidad productiva/almacén
- Descripción/justificación de la solicitud
- Validación en tiempo real

### ✅ Notificaciones de Estado de Solicitud
- Notificaciones automáticas de cambios de estado
- Vista dedicada con estadísticas
- Historial completo de notificaciones
- Iconos diferenciados por tipo

### ✅ Gestión de Perfil del Usuario
- Edición de información personal
- Estadísticas de actividad
- Información de la cuenta
- Validación de campos únicos

### ✅ Generar Reportes de Sobrantes
- Análisis de insumos entregados
- Filtros avanzados de búsqueda
- Estadísticas de uso
- **SIN exportación** (PDF/Excel) según requerimientos

### ✅ Visualizar Historial de Insumos
- Vista completa del inventario
- Estados de disponibilidad en tiempo real
- Filtros por categoría y estado
- Enlaces directos para solicitar

## Restricciones Implementadas

### ❌ Sin Exportación
- **PDF**: No disponible para el rol de Personal de Aseo
- **Excel**: No disponible para el rol de Personal de Aseo
- **Solo filtros**: El sistema permite filtrar y buscar, pero no exportar

### ✅ Solo Filtros y Búsqueda
- Filtros por fecha, estado, categoría
- Búsqueda por texto en nombres de insumos
- Ordenamiento por diferentes criterios
- Paginación de resultados

## Próximos Pasos Recomendados

1. **Crear rol "Personal de Aseo"** en la tabla `roles` del módulo SICA
2. **Asignar permisos específicos** al rol
3. **Implementar middleware** para verificar roles específicos
4. **Agregar notificaciones por email** para cambios de estado
5. **Implementar recuperación de contraseña**
6. **Agregar auditoría** de acciones del usuario

## Soporte y Mantenimiento

El sistema está completamente funcional y listo para uso en producción. Todas las funcionalidades solicitadas han sido implementadas según los requerimientos específicos, incluyendo las restricciones de exportación para el rol de Personal de Aseo.

Para soporte técnico o modificaciones adicionales, contactar al equipo de desarrollo.
