# MANUAL TÉCNICO - MÓDULO INFRASTOCK

## Sistema de Gestión de Inventario de Insumos, Herramientas y Equipos

**Versión:** 1.0  
**Fecha:** Febrero 2026  
**Plataforma:** SICEFA (Sistema de Información del Centro de Formación Agropecuario)  
**Módulo:** INFRASTOCK  

---

## TABLA DE CONTENIDO

- [INTRODUCCIÓN](#introducción)
- [OBJETIVO GENERAL](#objetivo-general)
- [1. DISEÑO TÉCNICO DEL SISTEMA](#1-diseño-técnico-del-sistema)
  - [1.1 Casos de uso](#11-casos-de-uso)
  - [1.2 Documento de requerimientos](#12-documento-de-requerimientos)
  - [1.3 Requisitos de hardware](#13-requisitos-de-hardware)
  - [1.4 Requisitos de software](#14-requisitos-de-software)
  - [1.5 Sistema operativo](#15-sistema-operativo)
  - [1.6 Servidor de base de datos](#16-servidor-de-base-de-datos)
  - [1.7 Navegadores compatibles](#17-navegadores-compatibles)
  - [1.8 Plataformas tecnológicas utilizadas](#18-plataformas-tecnológicas-utilizadas)
- [2. COMPONENTES Y ESTÁNDARES](#2-componentes-y-estándares)
  - [2.1 Frameworks](#21-frameworks)
  - [2.2 Librerías](#22-librerías)
  - [2.3 Patrones de diseño](#23-patrones-de-diseño)
  - [2.4 Protocolos de seguridad](#24-protocolos-de-seguridad)
- [3. MODELO DE BASE DE DATOS](#3-modelo-de-base-de-datos)
  - [3.1 Diagrama entidad-relación](#31-diagrama-entidad-relación)
  - [3.2 Diccionario de datos](#32-diccionario-de-datos)
- [4. DESPLIEGUE Y CONFIGURACIÓN](#4-despliegue-y-configuración)
  - [4.1 Diagrama de componentes](#41-diagrama-de-componentes)
  - [4.2 Diagrama de clases](#42-diagrama-de-clases)
  - [4.3 Instalación](#43-instalación)
  - [4.4 Configuración](#44-configuración)
  - [4.5 Despliegue](#45-despliegue)
- [5. RESOLUCIÓN DE PROBLEMAS](#5-resolución-de-problemas)
  - [5.1 Errores comunes](#51-errores-comunes)
  - [5.2 Posibles causas](#52-posibles-causas)
  - [5.3 Soluciones](#53-soluciones)
- [WEBLIOGRAFÍA](#webliografía)

---

## INTRODUCCIÓN

El módulo **INFRASTOCK** es un componente integral de la plataforma SICEFA (Sistema de Información del Centro de Formación Agropecuario), diseñado para la gestión completa del inventario de insumos, herramientas y equipos del centro de formación. Este sistema permite el control en tiempo real del stock disponible, la gestión de solicitudes de insumos por parte de múltiples roles organizacionales, el seguimiento de préstamos de herramientas y la generación de reportes de sobrantes.

El módulo implementa una arquitectura modular basada en Laravel que permite la separación de responsabilidades por roles, donde cada área productiva del centro (Agroindustria, Ganadería, Vigilancia, Centro de Convivencia, Ciencias Básicas, Psicola, Personal de Aseo, Operario e Instructor) tiene acceso a funcionalidades específicas según sus necesidades. El administrador tiene acceso completo a todas las funcionalidades del sistema, incluyendo la gestión de usuarios, aprobación de solicitudes, control de inventario y generación de reportes.

Este manual técnico documenta la arquitectura, componentes, configuraciones y procedimientos necesarios para el mantenimiento, despliegue y resolución de problemas del módulo INFRASTOCK.

---

## OBJETIVO GENERAL

Proporcionar una documentación técnica completa del módulo INFRASTOCK que permita a los desarrolladores, administradores de sistemas y personal técnico comprender la arquitectura interna, los componentes utilizados, la estructura de la base de datos y los procedimientos de instalación, configuración, despliegue y resolución de problemas del sistema de gestión de inventario.

---

## 1. DISEÑO TÉCNICO DEL SISTEMA

### 1.1 Casos de uso

#### CU-01: Gestión de Insumos (Administrador)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Administrador |
| **Descripción** | El administrador gestiona el inventario de insumos del centro |
| **Precondiciones** | El usuario debe estar autenticado con rol de Administrador |
| **Flujo Principal** | 1. Accede al panel de insumos. 2. Puede crear, editar, eliminar e importar insumos. 3. Visualiza stock en tiempo real. 4. Recibe alertas de stock bajo y vencimiento. |
| **Postcondiciones** | Los cambios se reflejan en el inventario y en las vistas de todos los roles |

#### CU-02: Solicitud de Insumos (Usuarios por Rol)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Personal de Aseo, Operario, Agroindustria, Ganadería, Vigilancia, Centro de Convivencia, Ciencias Básicas, Psicola |
| **Descripción** | Los usuarios solicitan insumos necesarios para sus labores |
| **Precondiciones** | El usuario debe estar autenticado con su rol correspondiente |
| **Flujo Principal** | 1. Accede a "Mis Solicitudes". 2. Clic en "Solicitar Insumo". 3. Selecciona insumos del catálogo con buscador. 4. Especifica cantidad y unidad productiva. 5. Envía la solicitud. 6. Recibe notificación de confirmación. |
| **Flujo Alternativo** | Si el insumo está agotado, no puede seleccionarse. Si la cantidad excede el stock, se muestra un error. |
| **Postcondiciones** | Se crea la solicitud en estado "pendiente" y se notifica al administrador |

#### CU-03: Aprobación/Rechazo de Solicitudes (Administrador)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Administrador |
| **Descripción** | El administrador revisa y aprueba o rechaza solicitudes de insumos |
| **Precondiciones** | Existen solicitudes en estado "pendiente" |
| **Flujo Principal** | 1. Accede al panel de solicitudes. 2. Revisa los detalles de la solicitud. 3. Aprueba o rechaza con justificación. 4. Se actualiza el stock y se notifica al usuario. |
| **Postcondiciones** | El estado de la solicitud se actualiza y se genera una notificación |

#### CU-04: Gestión de Préstamos de Herramientas (Instructor)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Instructor |
| **Descripción** | Los instructores solicitan préstamos de herramientas |
| **Precondiciones** | El usuario debe tener rol de Instructor |
| **Flujo Principal** | 1. Accede al dashboard de Instructor. 2. Solicita préstamo de herramienta. 3. Especifica propósito y fecha requerida. 4. El administrador aprueba el préstamo. 5. El instructor devuelve la herramienta. |
| **Postcondiciones** | Se registra el movimiento de préstamo y devolución |

#### CU-05: Reporte de Sobrantes (Todos los Roles)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Todos los roles de usuario |
| **Descripción** | Los usuarios reportan sobrantes de insumos no utilizados |
| **Precondiciones** | El usuario tiene solicitudes aprobadas con insumos entregados |
| **Flujo Principal** | 1. Accede a "Reporte de Sobrantes". 2. Selecciona la solicitud asociada. 3. Indica la cantidad sobrante y razón. 4. Envía el reporte. |
| **Postcondiciones** | Se registra el sobrante para revisión del administrador |

#### CU-06: Visualización de Stock en Tiempo Real (Todos los Roles)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Todos los roles de usuario |
| **Descripción** | Los usuarios visualizan el stock disponible de insumos |
| **Precondiciones** | El usuario está autenticado |
| **Flujo Principal** | 1. Accede a "Stock Disponible". 2. Filtra por categoría, estado o búsqueda. 3. Visualiza stock, estado y detalles de cada insumo. |
| **Postcondiciones** | Ninguna (solo lectura) |

#### CU-07: Gestión de Usuarios (Administrador)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Administrador |
| **Descripción** | El administrador registra y gestiona usuarios del sistema |
| **Precondiciones** | El usuario tiene rol de Administrador |
| **Flujo Principal** | 1. Accede a "Gestión de Usuarios". 2. Registra nuevos usuarios con roles. 3. Puede activar/desactivar cuentas. 4. Asigna roles específicos de INFRASTOCK. |
| **Postcondiciones** | El usuario registrado puede acceder al sistema con su rol |

#### CU-08: Sistema de Notificaciones (Todos los Roles)

| Campo | Descripción |
|-------|-------------|
| **Actor** | Todos los usuarios del sistema |
| **Descripción** | El sistema notifica cambios de estado en solicitudes y préstamos |
| **Precondiciones** | Existe un evento que genera notificación |
| **Flujo Principal** | 1. Se genera un evento (solicitud creada, aprobada, rechazada). 2. Se crea la notificación en base de datos. 3. El usuario la ve en el icono de campana. 4. Puede marcarla como leída. |
| **Postcondiciones** | La notificación queda registrada y visible para el usuario |

---

### 1.2 Documento de requerimientos

#### Requerimientos Funcionales

| ID | Requerimiento | Prioridad | Estado |
|----|---------------|-----------|--------|
| RF-01 | El sistema debe permitir la gestión CRUD de insumos (crear, leer, actualizar, eliminar) | Alta | Implementado |
| RF-02 | El sistema debe permitir la gestión CRUD de herramientas | Alta | Implementado |
| RF-03 | El sistema debe calcular automáticamente el stock disponible basado en movimientos | Alta | Implementado |
| RF-04 | El sistema debe permitir a los usuarios solicitar insumos con múltiples items | Alta | Implementado |
| RF-05 | El administrador debe poder aprobar o rechazar solicitudes con justificación | Alta | Implementado |
| RF-06 | El sistema debe gestionar préstamos y devoluciones de herramientas | Alta | Implementado |
| RF-07 | El sistema debe generar notificaciones automáticas por cambios de estado | Alta | Implementado |
| RF-08 | El sistema debe permitir filtrar y buscar insumos en tiempo real | Media | Implementado |
| RF-09 | El sistema debe gestionar categorías para insumos y herramientas | Media | Implementado |
| RF-10 | El sistema debe gestionar áreas productivas y almacenes | Media | Implementado |
| RF-11 | El sistema debe permitir el reporte de sobrantes de insumos | Media | Implementado |
| RF-12 | El sistema debe alertar sobre insumos con stock bajo o próximos a vencer | Media | Implementado |
| RF-13 | El sistema debe gestionar usuarios con roles específicos | Alta | Implementado |
| RF-14 | El sistema debe calcular automáticamente el estado de los insumos | Media | Implementado |
| RF-15 | El sistema debe permitir editar y eliminar solicitudes pendientes | Media | Implementado |

#### Requerimientos No Funcionales

| ID | Requerimiento | Prioridad | Estado |
|----|---------------|-----------|--------|
| RNF-01 | La interfaz debe ser responsive (compatible con dispositivos móviles) | Alta | Implementado |
| RNF-02 | El sistema debe cargar las vistas en menos de 3 segundos | Alta | Implementado |
| RNF-03 | El sistema debe utilizar CSRF tokens para protección contra ataques | Alta | Implementado |
| RNF-04 | El sistema debe implementar autenticación basada en sesiones | Alta | Implementado |
| RNF-05 | El sistema debe manejar errores con mensajes amigables (SweetAlert2) | Media | Implementado |
| RNF-06 | El sistema debe soportar soft deletes para integridad de datos | Media | Implementado |
| RNF-07 | El sistema debe ser compatible con los navegadores principales | Alta | Implementado |

---

### 1.3 Requisitos de hardware

#### Servidor de Desarrollo

| Componente | Requisito Mínimo | Recomendado |
|------------|-----------------|-------------|
| **Procesador** | Intel Core i3 / AMD Ryzen 3 (2 cores) | Intel Core i5 / AMD Ryzen 5 (4 cores) |
| **Memoria RAM** | 4 GB | 8 GB o superior |
| **Almacenamiento** | 20 GB SSD | 50 GB SSD |
| **Red** | 10 Mbps | 100 Mbps |

#### Servidor de Producción

| Componente | Requisito Mínimo | Recomendado |
|------------|-----------------|-------------|
| **Procesador** | 2 vCPUs | 4 vCPUs |
| **Memoria RAM** | 4 GB | 8 GB o superior |
| **Almacenamiento** | 40 GB SSD | 100 GB SSD |
| **Red** | 100 Mbps | 1 Gbps |
| **Ancho de Banda** | 1 TB/mes | Ilimitado |

#### Estación de Trabajo del Usuario

| Componente | Requisito Mínimo |
|------------|-----------------|
| **Procesador** | Cualquier procesador moderno (2015+) |
| **Memoria RAM** | 2 GB |
| **Pantalla** | Resolución mínima 1024x768 |
| **Red** | 5 Mbps |

---

### 1.4 Requisitos de software

| Software | Versión Mínima | Versión Recomendada | Propósito |
|----------|---------------|---------------------|-----------|
| **PHP** | 8.1 | 8.2+ | Lenguaje de programación del backend |
| **Composer** | 2.0 | 2.6+ | Gestor de dependencias de PHP |
| **Laravel** | 9.x | 10.x | Framework principal de la aplicación |
| **MySQL/MariaDB** | 5.7 / 10.4 | 8.0 / 10.11+ | Base de datos relacional |
| **Node.js** | 16.x | 18.x+ | Compilación de assets frontend |
| **NPM** | 8.x | 9.x+ | Gestor de paquetes de JavaScript |
| **Git** | 2.30 | 2.40+ | Control de versiones |
| **Apache/Nginx** | 2.4 / 1.18 | 2.4+ / 1.24+ | Servidor web |

---

### 1.5 Sistema operativo

El módulo INFRASTOCK es compatible con los siguientes sistemas operativos:

| Sistema Operativo | Versión | Entorno |
|-------------------|---------|---------|
| **Windows** | 10/11 | Desarrollo (con Laragon, XAMPP o similar) |
| **Ubuntu Server** | 20.04 LTS / 22.04 LTS | Producción |
| **Debian** | 11 / 12 | Producción |
| **CentOS** | 8+ / Rocky Linux 8+ | Producción |
| **macOS** | 12+ (Monterey) | Desarrollo |

**Entorno de desarrollo recomendado:** Windows 10/11 con **Laragon** (incluye Apache, MySQL, PHP y Node.js preconfigurados).

---

### 1.6 Servidor de base de datos

| Característica | Detalle |
|----------------|---------|
| **Motor** | MySQL 8.0 / MariaDB 10.4+ |
| **Charset** | utf8mb4 |
| **Collation** | utf8mb4_unicode_ci |
| **Motor de almacenamiento** | InnoDB (soporte para transacciones y foreign keys) |
| **Puerto por defecto** | 3306 |
| **Nombre de la base de datos** | `sicefa` (compartida con otros módulos) |

**Tablas principales del módulo:**

| Tabla | Descripción | Registros aproximados |
|-------|-------------|----------------------|
| `infrastock_categories` | Categorías de insumos y herramientas | ~20 |
| `equipments` | Insumos y equipos del inventario | ~200+ |
| `tools` | Herramientas del inventario | ~50+ |
| `requests` | Solicitudes de insumos | Variable |
| `request_items` | Items individuales de cada solicitud | Variable |
| `surpluses` | Reportes de sobrantes | Variable |
| `warehouse_movements` | Movimientos de almacén (entregas, préstamos) | Variable |
| `productive_units` | Unidades productivas/áreas | ~10 |
| `warehouses` | Almacenes/bodegas | ~5 |
| `productive_unit_warehouses` | Relación unidad-almacén | ~15 |
| `notifications` | Notificaciones del sistema | Variable |

---

### 1.7 Navegadores compatibles

| Navegador | Versión Mínima | Soporte |
|-----------|---------------|---------|
| **Google Chrome** | 90+ | Completo |
| **Mozilla Firefox** | 88+ | Completo |
| **Microsoft Edge** | 90+ (Chromium) | Completo |
| **Safari** | 14+ | Completo |
| **Opera** | 76+ | Completo |
| **Samsung Internet** | 14+ | Parcial (móvil) |
| **Internet Explorer** | N/A | No soportado |

**Nota:** Se recomienda utilizar la última versión estable de Google Chrome o Mozilla Firefox para una experiencia óptima.

---

### 1.8 Plataformas tecnológicas utilizadas

| Tecnología | Versión | Uso en el Proyecto |
|------------|---------|-------------------|
| **PHP** | 8.1+ | Lenguaje backend principal |
| **Laravel** | 9.x / 10.x | Framework MVC del backend |
| **nwidart/laravel-modules** | 10.x | Arquitectura modular |
| **MySQL/MariaDB** | 8.0 / 10.4+ | Base de datos relacional |
| **Tailwind CSS** | 3.x (CDN) | Framework CSS para estilos |
| **Alpine.js** | 3.x (CDN) | Framework JavaScript reactivo |
| **jQuery** | 3.7.x | Manipulación DOM y AJAX |
| **Chart.js** | 4.x (CDN) | Visualización de gráficos |
| **DataTables** | 1.13.x | Tablas interactivas |
| **SweetAlert2** | 11.x | Alertas y confirmaciones |
| **Font Awesome** | 6.5.x | Iconografía |
| **Google Fonts** | N/A | Tipografías (Plus Jakarta Sans, Dancing Script) |

---

## 2. COMPONENTES Y ESTÁNDARES

### 2.1 Frameworks

#### Laravel 9.x / 10.x (Backend)

Laravel es el framework PHP principal utilizado para toda la lógica del servidor. Proporciona:

- **Eloquent ORM:** Mapeo objeto-relacional para interacción con la base de datos.
- **Blade:** Motor de plantillas para las vistas.
- **Artisan:** Herramienta de línea de comandos para tareas administrativas.
- **Migrations:** Sistema de control de versiones para la base de datos.
- **Middleware:** Pipeline de procesamiento de solicitudes HTTP.
- **Service Providers:** Registro y configuración de servicios.
- **Form Request Validation:** Validación de datos de entrada.

#### nwidart/laravel-modules (Arquitectura Modular)

Permite organizar la aplicación en módulos independientes. INFRASTOCK es uno de estos módulos dentro de la plataforma SICEFA, junto con otros módulos como SICA, AGROINDUSTRIA, etc.

#### Tailwind CSS 3.x (Frontend)

Framework CSS utility-first utilizado para todos los estilos de la interfaz. Se carga vía CDN para simplificar el despliegue.

#### Alpine.js 3.x (Frontend Reactivo)

Framework JavaScript ligero utilizado para:
- Interactividad del sidebar (expandir/colapsar).
- Dropdowns de notificaciones y menú de usuario.
- Toggle de elementos de la interfaz.
- Transiciones y animaciones.

---

### 2.2 Librerías

| Librería | Versión | Propósito |
|----------|---------|-----------|
| **jQuery** | 3.7.1 | Manipulación DOM, eventos y peticiones AJAX |
| **DataTables** | 1.13.7 | Tablas con paginación, búsqueda y ordenamiento |
| **Chart.js** | 4.x | Gráficos de barras para consumo por área |
| **SweetAlert2** | 11.x | Diálogos de confirmación, alertas de éxito/error |
| **Font Awesome** | 6.5.1 | Iconos vectoriales para la interfaz |
| **Google Fonts** | N/A | Tipografías: Plus Jakarta Sans (texto), Dancing Script (eslogan) |

#### Librerías PHP (Composer)

| Paquete | Propósito |
|---------|-----------|
| `nwidart/laravel-modules` | Arquitectura modular |
| `maatwebsite/excel` | Exportación a Excel (si disponible) |
| `barryvdh/laravel-dompdf` | Generación de PDFs (si disponible) |

---

### 2.3 Patrones de diseño

#### MVC (Modelo-Vista-Controlador)

Patrón arquitectónico principal del módulo:

- **Modelo (Entities):** 14 modelos Eloquent que representan las entidades del dominio.
- **Vista (Blade Templates):** Plantillas organizadas por rol con herencia de layouts.
- **Controlador (Controllers):** 23 controladores que manejan la lógica de negocio.

#### Repository Pattern (Implícito)

Los controladores interactúan con los modelos Eloquent para acceder a los datos, utilizando scopes y relaciones para consultas complejas.

#### Observer Pattern

- Evento `creating` en el modelo `Equipment` para calcular automáticamente el estado antes de guardar.
- Middleware `ShareNotifications` que inyecta datos de notificaciones en todas las vistas.

#### Strategy Pattern

El sistema de roles implementa estrategias diferentes para cada tipo de usuario:
- Cada rol tiene su propio controlador con lógica específica.
- El layout `usuarios-master.blade.php` detecta el rol del usuario para mostrar menús dinámicos.

#### Template Method Pattern

La herencia de layouts Blade implementa este patrón:
```
base-master.blade.php (layout base)
    ├── master.blade.php (administrador)
    ├── instructor-master.blade.php (instructor)
    └── usuarios-master.blade.php (roles de usuario)
```

#### Factory Pattern

Los seeders utilizan el patrón Factory para crear datos iniciales del sistema.

#### Soft Delete Pattern

La mayoría de las entidades implementan borrado lógico (`SoftDeletes`) para mantener la integridad referencial y permitir la recuperación de datos.

#### Accessor/Mutator Pattern

El modelo `Equipment` utiliza accessors para calcular dinámicamente:
- `stock` = initial_amount - used_amount
- `status` = calculado según stock, fecha de vencimiento y stock mínimo
- `used_amount` = suma de entregas - suma de recepciones

---

### 2.4 Protocolos de seguridad

#### Autenticación

| Mecanismo | Descripción |
|-----------|-------------|
| **Autenticación basada en sesiones** | Laravel Auth con sesiones almacenadas en servidor |
| **Hashing de contraseñas** | Bcrypt con salt automático (Laravel Hash) |
| **Protección de sesiones** | Regeneración de ID de sesión tras login |
| **Timeout de sesión** | Configurable en `config/session.php` |

#### Autorización

| Mecanismo | Descripción |
|-----------|-------------|
| **Control de acceso por roles** | Verificación de rol en cada controlador (`verifyRole()`) |
| **Middleware de autenticación** | `auth` middleware en todas las rutas protegidas |
| **Verificación de propiedad** | Las solicitudes solo son accesibles por su creador |
| **Middleware personalizado** | `VerifyAdminRole` para rutas administrativas |

#### Protección contra ataques

| Ataque | Protección |
|--------|-----------|
| **CSRF** | Token CSRF en todos los formularios (`@csrf`) y meta tag en el layout |
| **XSS** | Escapado automático de Blade (`{{ }}`) y validación de entrada |
| **SQL Injection** | Eloquent ORM con prepared statements |
| **Mass Assignment** | Propiedad `$fillable` en todos los modelos |
| **Session Hijacking** | Cookies HTTP-Only, Secure y SameSite |

#### Encriptación

| Elemento | Método |
|----------|--------|
| **Contraseñas** | Bcrypt (12 rounds) |
| **Cookies** | Middleware `EncryptCookies` |
| **Sesiones** | Encriptación AES-256-CBC |
| **CSRF Token** | Token aleatorio por sesión |

---

## 3. MODELO DE BASE DE DATOS

### 3.1 Diagrama entidad-relación

```
┌──────────────────────────────────────────────────────────────────┐
│                    DIAGRAMA ENTIDAD-RELACIÓN                     │
│                      MÓDULO INFRASTOCK                           │
└──────────────────────────────────────────────────────────────────┘

┌─────────────┐     1     ┌──────────────────────┐     *     ┌──────────────┐
│    User     │◄──────────│       Request         │──────────►│ RequestItem  │
│             │           │                      │           │              │
│ -id         │     *     │ -id                  │     1     │ -id          │
│ -name       │◄──────────│ -user_id (FK)        │           │ -request_id  │
│ -email      │ approved  │ -productive_unit_    │           │ -equipment_id│
│ -password   │   _by     │  warehouse_id (FK)   │           │ -requested_  │
└──────┬──────┘           │ -description         │           │  amount      │
       │                  │ -status              │           │ -approved_   │
       │                  │ -approved_at         │           │  amount      │
       │                  │ -rejected_at         │           │ -status      │
       │                  │ -rejection_reason    │           └──────┬───────┘
       │                  └──────────┬───────────┘                  │
       │                             │                              │ *
       │                             │ *                            │
       │ *                    ┌──────▼───────┐               ┌─────▼────────┐
┌──────▼──────┐               │   Surplus    │               │  Equipment   │
│Notification │               │              │               │              │
│             │               │ -id          │     *         │ -id          │
│ -id         │               │ -equipment_id│◄──────────────│ -name        │
│ -type       │               │ -user_id     │               │ -amount      │
│ -notifiable │               │ -request_id  │               │ -initial_    │
│  _type      │               │ -surplus_    │               │  amount      │
│ -notifiable │               │  amount      │               │ -price       │
│  _id        │               │ -status      │               │ -category_id │
│ -data       │               └──────────────┘               │ -expiration_ │
│ -read_at    │                                              │  date        │
└─────────────┘                                              │ -status      │
                                                             └──────┬───────┘
                                                                    │ *
                                                                    │
                                                             ┌──────▼───────┐
                                                             │ Infrastock   │
                                                             │ Category     │
                                                             │              │
                                                             │ -id          │
                                                             │ -name        │
                                                             │ -type        │
                                                             └──────┬───────┘
                                                                    │ *
                                                                    │
                                                             ┌──────▼───────┐
                                                             │    Tool      │
                                                             │              │
                                                             │ -id          │
                                                             │ -nombre      │
                                                             │ -placa       │
                                                             │ -category_id │
                                                             │ -estado      │
                                                             │ -amount      │
                                                             └──────────────┘

┌────────────────┐    *    ┌──────────────────────────┐    *    ┌──────────────┐
│ ProductiveUnit │◄────────│ ProductiveUnitWarehouse   │────────►│  Warehouse   │
│                │         │                          │         │              │
│ -id            │    1    │ -id                      │    1    │ -id          │
│ -name          │         │ -productive_unit_id (FK) │         │ -name        │
│ -description   │         │ -warehouse_id (FK)       │         │ -description │
└────────────────┘         └────────────┬─────────────┘         └──────────────┘
                                        │
                                        │ 1
                                        │
                               ┌────────▼────────────┐
                               │ WarehouseMovement    │
                               │                     │
                               │ -id                 │
                               │ -productive_unit_   │
                               │  warehouse_id (FK)  │
                               │ -equipment_id (FK)  │
                               │ -user_id (FK)       │
                               │ -item_type          │
                               │ -role               │
                               │ -amount             │
                               │ -status             │
                               └─────────────────────┘
```

---

### 3.2 Diccionario de datos

#### Tabla: `infrastock_categories`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | NO | - | Nombre de la categoría (único) |
| `type` | VARCHAR(255) | NO | - | Tipo: 'supply' o 'tool' |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de última actualización |
| `deleted_at` | TIMESTAMP | SÍ | NULL | Fecha de borrado lógico |

#### Tabla: `equipments`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `labor_id` | BIGINT UNSIGNED | NO | - | FK → labors.id |
| `inventory_id` | BIGINT UNSIGNED | NO | - | FK → inventories.id |
| `name` | VARCHAR(255) | SÍ | NULL | Nombre del insumo |
| `characteristics` | TEXT | SÍ | NULL | Características del insumo |
| `amount` | INT | NO | 0 | Cantidad actual |
| `initial_amount` | INT | NO | 0 | Cantidad inicial |
| `minimum_stock` | INT | NO | 0 | Stock mínimo de alerta |
| `unit_measure` | VARCHAR(50) | SÍ | NULL | Unidad de medida |
| `price` | DECIMAL(10,2) | NO | 0.00 | Precio unitario |
| `category_id` | BIGINT UNSIGNED | SÍ | NULL | FK → infrastock_categories.id |
| `expiration_date` | DATE | SÍ | NULL | Fecha de vencimiento |
| `observations` | TEXT | SÍ | NULL | Observaciones adicionales |
| `status` | ENUM | SÍ | 'disponible' | Estado: disponible, agotado, vencido, bajo_stock, critico |
| `deleted_at` | TIMESTAMP | SÍ | NULL | Fecha de borrado lógico |

#### Tabla: `tools`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `nombre` | VARCHAR(255) | SÍ | NULL | Nombre de la herramienta |
| `imagen` | VARCHAR(255) | SÍ | NULL | Ruta de imagen |
| `placa` | VARCHAR(255) | SÍ | NULL | Código de placa |
| `descripcion` | TEXT | SÍ | NULL | Descripción original |
| `descripcion_actual` | TEXT | SÍ | NULL | Descripción actual |
| `marca` | VARCHAR(255) | SÍ | NULL | Marca |
| `modelo` | VARCHAR(255) | SÍ | NULL | Modelo |
| `category_id` | BIGINT UNSIGNED | SÍ | NULL | FK → infrastock_categories.id |
| `estado` | ENUM | SÍ | 'disponible' | Estado: disponible, en_prestamo, mantenimiento, no_disponible |
| `cantidad_total` | INT | SÍ | NULL | Cantidad total |
| `cantidad_disponible` | INT | SÍ | NULL | Cantidad disponible |
| `fecha_adquisicion` | DATE | SÍ | NULL | Fecha de adquisición |
| `fecha_mantenimiento` | DATE | SÍ | NULL | Último mantenimiento |
| `proximo_mantenimiento` | DATE | SÍ | NULL | Próximo mantenimiento |
| `atributos` | TEXT | SÍ | NULL | Atributos adicionales |
| `labor_id` | BIGINT UNSIGNED | SÍ | NULL | FK → labors.id |
| `inventory_id` | BIGINT UNSIGNED | SÍ | NULL | FK → inventories.id |
| `amount` | INT | SÍ | NULL | Cantidad |
| `price` | DECIMAL(10,2) | SÍ | NULL | Precio |
| `deleted_at` | TIMESTAMP | SÍ | NULL | Fecha de borrado lógico |

#### Tabla: `requests`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `user_id` | BIGINT UNSIGNED | SÍ | NULL | FK → users.id (solicitante) |
| `productive_unit_warehouse_id` | BIGINT UNSIGNED | SÍ | NULL | FK → productive_unit_warehouses.id |
| `description` | TEXT | SÍ | NULL | Descripción/justificación |
| `status` | ENUM | NO | 'pending' | Estado: pending, approved, rejected |
| `approved_at` | TIMESTAMP | SÍ | NULL | Fecha de aprobación |
| `rejected_at` | TIMESTAMP | SÍ | NULL | Fecha de rechazo |
| `approved_by` | BIGINT UNSIGNED | SÍ | NULL | FK → users.id (aprobador) |
| `rejection_reason` | TEXT | SÍ | NULL | Razón de rechazo |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

#### Tabla: `request_items`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `request_id` | BIGINT UNSIGNED | NO | - | FK → requests.id |
| `equipment_id` | BIGINT UNSIGNED | NO | - | FK → equipments.id |
| `requested_amount` | INT | NO | - | Cantidad solicitada |
| `approved_amount` | INT | SÍ | NULL | Cantidad aprobada |
| `delivered_amount` | INT | SÍ | NULL | Cantidad entregada |
| `status` | VARCHAR(255) | NO | 'pending' | Estado: pending, approved, rejected, delivered |
| `notes` | TEXT | SÍ | NULL | Notas adicionales |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

#### Tabla: `surpluses`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `equipment_id` | BIGINT UNSIGNED | NO | - | FK → equipments.id |
| `user_id` | BIGINT UNSIGNED | NO | - | FK → users.id |
| `request_id` | BIGINT UNSIGNED | SÍ | NULL | FK → requests.id |
| `request_item_id` | BIGINT UNSIGNED | SÍ | NULL | FK → request_items.id |
| `surplus_amount` | INT | NO | 0 | Cantidad sobrante |
| `reason` | TEXT | SÍ | NULL | Razón del sobrante |
| `description` | TEXT | SÍ | NULL | Descripción detallada |
| `surplus_date` | DATE | NO | - | Fecha del reporte |
| `status` | ENUM | NO | 'pending' | Estado: pending, approved, rejected |
| `processed_at` | TIMESTAMP | SÍ | NULL | Fecha de procesamiento |
| `processed_by` | VARCHAR(255) | SÍ | NULL | Procesado por |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

#### Tabla: `warehouse_movements`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `productive_unit_warehouse_id` | BIGINT UNSIGNED | NO | - | FK → productive_unit_warehouses.id |
| `movement_id` | BIGINT UNSIGNED | SÍ | NULL | ID de referencia al item |
| `equipment_id` | BIGINT UNSIGNED | SÍ | NULL | FK → equipments.id |
| `role` | ENUM | NO | - | Tipo: Entrega, Recibe, Préstamo, Devolución |
| `user_id` | BIGINT UNSIGNED | SÍ | NULL | FK → users.id |
| `item_type` | VARCHAR(255) | SÍ | NULL | Tipo: 'equipment' o 'tool' |
| `amount` | INT | SÍ | NULL | Cantidad del movimiento |
| `status` | ENUM | SÍ | 'pending' | Estado: pending, approved, rejected |
| `surplus_id` | BIGINT UNSIGNED | SÍ | NULL | FK → surpluses.id |
| `description` | TEXT | SÍ | NULL | Descripción |
| `imagen` | VARCHAR(255) | SÍ | NULL | Imagen del movimiento |
| `purpose` | TEXT | SÍ | NULL | Propósito del préstamo |
| `required_date` | DATE | SÍ | NULL | Fecha requerida |
| `delivery_image` | VARCHAR(255) | SÍ | NULL | Imagen de entrega |
| `return_image` | VARCHAR(255) | SÍ | NULL | Imagen de devolución |
| `deleted_at` | TIMESTAMP | SÍ | NULL | Fecha de borrado lógico |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

#### Tabla: `productive_unit_warehouses`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `productive_unit_id` | BIGINT UNSIGNED | NO | - | FK → productive_units.id |
| `warehouse_id` | BIGINT UNSIGNED | NO | - | FK → warehouses.id |
| `deleted_at` | TIMESTAMP | SÍ | NULL | Fecha de borrado lógico |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

#### Tabla: `notifications`

| Columna | Tipo | Nulo | Default | Descripción |
|---------|------|------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Identificador único |
| `type` | VARCHAR(255) | NO | - | Tipo de notificación |
| `notifiable_type` | VARCHAR(255) | NO | - | Modelo polimórfico |
| `notifiable_id` | BIGINT UNSIGNED | NO | - | ID del modelo notificable |
| `data` | JSON | NO | - | Datos de la notificación |
| `read_at` | TIMESTAMP | SÍ | NULL | Fecha de lectura |
| `created_at` | TIMESTAMP | SÍ | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | SÍ | NULL | Fecha de actualización |

---

## 4. DESPLIEGUE Y CONFIGURACIÓN

### 4.1 Diagrama de componentes

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          MÓDULO INFRASTOCK                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌─── HTTP Layer ─────────────────────────────────────────────────┐     │
│  │                                                                │     │
│  │  ┌─────────────┐  ┌──────────────┐  ┌───────────────────┐    │     │
│  │  │ Routes      │  │ Middleware   │  │ Controllers (23)  │    │     │
│  │  │ web.php     │→ │ Auth         │→ │ INFRASTOCKCtrl    │    │     │
│  │  │ api.php     │  │ ShareNotif   │  │ AdminRequestCtrl  │    │     │
│  │  │ (~200 rutas)│  │ VerifyAdmin  │  │ CleaningStaffCtrl │    │     │
│  │  │             │  │ CSRF         │  │ OperatorCtrl      │    │     │
│  │  └─────────────┘  └──────────────┘  │ AgroindustriaCtrl │    │     │
│  │                                      │ GanaderiaCtrl     │    │     │
│  │                                      │ ConvivenciaCtrl   │    │     │
│  │                                      │ VigilanciaCtrl    │    │     │
│  │                                      │ CienciasBasicasC  │    │     │
│  │                                      │ PsicolaCtrl       │    │     │
│  │                                      │ InstructorCtrl    │    │     │
│  │                                      │ ToolCtrl          │    │     │
│  │                                      │ SupplyCtrl        │    │     │
│  │                                      │ LoanCtrl          │    │     │
│  │                                      │ SurplusCtrl       │    │     │
│  │                                      │ ...               │    │     │
│  │                                      └───────────────────┘    │     │
│  └────────────────────────────────────────────────────────────────┘     │
│                                                                         │
│  ┌─── Business Layer ─────────────────────────────────────────────┐     │
│  │                                                                │     │
│  │  ┌──────────────────┐  ┌──────────────────┐                   │     │
│  │  │ Entities (14)    │  │ Notifications    │                   │     │
│  │  │ Equipment        │  │ Mail (2 classes) │                   │     │
│  │  │ Tool             │  └──────────────────┘                   │     │
│  │  │ Request          │                                          │     │
│  │  │ RequestItem      │  ┌──────────────────┐                   │     │
│  │  │ Surplus          │  │ Console Commands │                   │     │
│  │  │ WarehouseMovement│  │ (7 commands)     │                   │     │
│  │  │ InfrastockCategory│ └──────────────────┘                   │     │
│  │  │ Notification     │                                          │     │
│  │  │ ...              │  ┌──────────────────┐                   │     │
│  │  └──────────────────┘  │ Exports (4)      │                   │     │
│  │                         │ PDF / Excel      │                   │     │
│  │                         └──────────────────┘                   │     │
│  └────────────────────────────────────────────────────────────────┘     │
│                                                                         │
│  ┌─── Data Layer ─────────────────────────────────────────────────┐     │
│  │                                                                │     │
│  │  ┌──────────────────┐  ┌──────────────────┐                   │     │
│  │  │ Migrations (26)  │  │ Seeders (7)      │                   │     │
│  │  │ Schema changes   │  │ Initial data     │                   │     │
│  │  └──────────────────┘  └──────────────────┘                   │     │
│  │                                                                │     │
│  │           ┌──────────────────────────┐                         │     │
│  │           │     MySQL / MariaDB      │                         │     │
│  │           │  Base de datos: sicefa   │                         │     │
│  │           │  (11 tablas principales) │                         │     │
│  │           └──────────────────────────┘                         │     │
│  └────────────────────────────────────────────────────────────────┘     │
│                                                                         │
│  ┌─── Presentation Layer ─────────────────────────────────────────┐     │
│  │                                                                │     │
│  │  ┌──────────────────────────────────────────────────────┐     │     │
│  │  │                   Layouts                            │     │     │
│  │  │  base-master.blade.php (layout base compartido)      │     │     │
│  │  │    ├── master.blade.php (administrador)              │     │     │
│  │  │    ├── instructor-master.blade.php (instructor)      │     │     │
│  │  │    └── usuarios-master.blade.php (roles de usuario)  │     │     │
│  │  └──────────────────────────────────────────────────────┘     │     │
│  │                                                                │     │
│  │  ┌──────────────────────────────────────────────────────┐     │     │
│  │  │              Vistas por Rol                          │     │     │
│  │  │  admin/ | cleaning-staff/ | operator/ | instructor/  │     │     │
│  │  │  agroindustria/ | ganaderia/ | vigilancia/           │     │     │
│  │  │  convivencia/ | ciencias-basicas/ | psicola/         │     │     │
│  │  └──────────────────────────────────────────────────────┘     │     │
│  │                                                                │     │
│  │  ┌──────────────────────────────────────────────────────┐     │     │
│  │  │           Frontend (CDN)                             │     │     │
│  │  │  Tailwind CSS | Alpine.js | jQuery | Chart.js        │     │     │
│  │  │  DataTables | SweetAlert2 | Font Awesome             │     │     │
│  │  └──────────────────────────────────────────────────────┘     │     │
│  └────────────────────────────────────────────────────────────────┘     │
│                                                                         │
│  ┌─── Config & Providers ─────────────────────────────────────────┐     │
│  │  INFRASTOCKServiceProvider | RouteServiceProvider              │     │
│  │  config.php | module.json                                      │     │
│  └────────────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────────────┘
```

---

### 4.2 Diagrama de clases

```
┌─────────────────────────────────────────────────────────────────┐
│                    DIAGRAMA DE CLASES                            │
│                    MÓDULO INFRASTOCK                             │
└─────────────────────────────────────────────────────────────────┘

┌────────────────────────┐         ┌────────────────────────┐
│    <<Model>>           │         │    <<Model>>           │
│    Equipment           │         │    Tool                │
├────────────────────────┤         ├────────────────────────┤
│ -id: int               │         │ -id: int               │
│ -name: string          │         │ -nombre: string        │
│ -amount: int           │         │ -placa: string         │
│ -initial_amount: int   │         │ -marca: string         │
│ -minimum_stock: int    │         │ -modelo: string        │
│ -unit_measure: string  │         │ -estado: string        │
│ -price: decimal        │         │ -cantidad_total: int   │
│ -category_id: int      │         │ -category_id: int      │
│ -expiration_date: date │         │ -amount: int           │
│ -status: string        │         │ -price: decimal        │
├────────────────────────┤         ├────────────────────────┤
│ +getStockAttribute()   │         │ +category()            │
│ +hasStockFor()         │         │ +labor()               │
│ +calculateStatus()     │         │ +inventory()           │
│ +category()            │         └───────────┬────────────┘
│ +labor()               │                     │
│ +inventory()           │                     │ belongsTo
└───────────┬────────────┘                     │
            │                           ┌──────▼───────────────┐
            │ belongsTo                 │    <<Model>>         │
            │                           │  InfrastockCategory  │
     ┌──────▼───────────────┐           ├──────────────────────┤
     │    <<Model>>         │           │ -id: int             │
     │  InfrastockCategory  │           │ -name: string        │
     │                      │           │ -type: string        │
     └──────────────────────┘           ├──────────────────────┤
                                        │ +equipments()        │
                                        │ +tools()             │
                                        └──────────────────────┘

┌────────────────────────┐         ┌────────────────────────┐
│    <<Model>>           │ hasMany │    <<Model>>           │
│    Request             │────────►│    RequestItem         │
├────────────────────────┤         ├────────────────────────┤
│ -id: int               │         │ -id: int               │
│ -user_id: int          │         │ -request_id: int       │
│ -productive_unit_      │         │ -equipment_id: int     │
│  warehouse_id: int     │         │ -requested_amount: int │
│ -description: string   │         │ -approved_amount: int  │
│ -status: string        │         │ -status: string        │
│ -approved_by: int      │         ├────────────────────────┤
│ -rejection_reason: str │         │ +request()             │
├────────────────────────┤         │ +equipment()           │
│ +user()                │         └────────────────────────┘
│ +approver()            │
│ +productiveUnit        │
│  Warehouse()           │         ┌────────────────────────┐
│ +items()               │         │    <<Model>>           │
│ +scopePending()        │         │    Surplus             │
│ +scopeApproved()       │         ├────────────────────────┤
└────────────────────────┘         │ -id: int               │
                                    │ -equipment_id: int     │
┌────────────────────────┐         │ -user_id: int          │
│    <<Model>>           │         │ -request_id: int       │
│  WarehouseMovement     │         │ -surplus_amount: int   │
├────────────────────────┤         │ -status: string        │
│ -id: int               │         ├────────────────────────┤
│ -productive_unit_      │         │ +equipment()           │
│  warehouse_id: int     │         │ +user()                │
│ -equipment_id: int     │         │ +request()             │
│ -user_id: int          │         │ +isPending()           │
│ -item_type: string     │         └────────────────────────┘
│ -role: string          │
│ -amount: int           │         ┌────────────────────────┐
│ -status: string        │         │    <<Model>>           │
├────────────────────────┤         │    Notification        │
│ +productiveUnit        │         ├────────────────────────┤
│  Warehouse()           │         │ -id: int               │
│ +user()                │         │ -type: string          │
│ +equipment()           │         │ -notifiable_type: str  │
│ +tool()                │         │ -notifiable_id: int    │
│ +surplus()             │         │ -data: array           │
└────────────────────────┘         │ -read_at: datetime     │
                                    ├────────────────────────┤
┌────────────────────────┐         │ +notifiable()          │
│ ProductiveUnitWarehouse│         │ +markAsRead()          │
├────────────────────────┤         │ +isRead()              │
│ -id: int               │         └────────────────────────┘
│ -productive_unit_id    │
│ -warehouse_id          │
├────────────────────────┤
│ +productiveUnit()      │
│ +warehouse()           │
└────────────────────────┘
```

**Nota completa:** El diagrama de clases detallado en formato PlantUML y Mermaid está disponible en el archivo `DIAGRAMA_CLASES.md`.

---

### 4.3 Instalación

#### Paso 1: Requisitos previos

Asegúrese de tener instalados:

```bash
# Verificar versiones
php -v          # PHP 8.1+
composer -V     # Composer 2.0+
mysql --version # MySQL 8.0+ o MariaDB 10.4+
node -v         # Node.js 16+
npm -v          # NPM 8+
```

#### Paso 2: Clonar el repositorio

```bash
git clone <url-repositorio> sicefa
cd sicefa
```

#### Paso 3: Instalar dependencias

```bash
# Dependencias PHP
composer install

# Dependencias JavaScript
npm install
```

#### Paso 4: Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con las credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sicefa
DB_USERNAME=root
DB_PASSWORD=
```

#### Paso 5: Ejecutar migraciones y seeders

```bash
# Ejecutar todas las migraciones
php artisan migrate

# Ejecutar los seeders del módulo INFRASTOCK
php artisan module:seed INFRASTOCK
```

#### Paso 6: Verificar la instalación

```bash
# Listar módulos habilitados
php artisan module:list

# Verificar que INFRASTOCK aparezca como habilitado
```

---

### 4.4 Configuración

#### Configuración del módulo

**Archivo:** `Modules/INFRASTOCK/Config/config.php`

```php
return [
    'name' => 'INFRASTOCK',
];
```

**Archivo:** `Modules/INFRASTOCK/module.json`

```json
{
    "name": "INFRASTOCK",
    "alias": "infrastock",
    "description": "Módulo de gestión de inventario de insumos, herramientas y equipos",
    "priority": 0,
    "providers": [
        "Modules\\INFRASTOCK\\Providers\\INFRASTOCKServiceProvider"
    ]
}
```

#### Configuración del Service Provider

**Archivo:** `Modules/INFRASTOCK/Providers/INFRASTOCKServiceProvider.php`

Registra:
- Rutas web y API del módulo.
- Vistas con namespace `infrastock::`.
- Migraciones de la base de datos.
- Configuraciones del módulo.
- Traducciones.

#### Roles del sistema

El módulo define 10 roles principales:

| Rol | Slug | Descripción |
|-----|------|-------------|
| Administrador | infrastock.admin | Acceso completo al sistema |
| Operario | operario | Solicitud de insumos generales |
| Personal de Aseo | aseo | Solicitud de insumos de limpieza |
| Ganadería | infrastock.ganaderia | Solicitud de insumos pecuarios |
| Centro de Convivencia | infrastock.centro-convivencia | Solicitud de insumos |
| Vigilancia | infrastock.vigilancia | Solicitud de insumos de seguridad |
| Agroindustria | infrastock.agroindustria | Solicitud de insumos agroindustriales |
| Ciencias Básicas | infrastock.ciencias-basicas | Solicitud de insumos de laboratorio |
| Psicola | infrastock.psicola | Solicitud de insumos piscícolas |
| Instructor | infrastock.instructor | Préstamo de herramientas |

---

### 4.5 Despliegue

#### Despliegue en desarrollo (Laragon)

```bash
# 1. Colocar el proyecto en C:\laragon\www\SICEFA
# 2. Laragon detecta automáticamente el proyecto
# 3. Acceder a http://sicefa.test o http://127.0.0.1:8000

# Iniciar servidor de desarrollo de Laravel
php artisan serve
```

#### Despliegue en producción (Ubuntu Server)

```bash
# 1. Configurar servidor web (Nginx o Apache)
# 2. Configurar SSL (Let's Encrypt)
# 3. Optimizar Laravel

# Generar caché de configuración
php artisan config:cache

# Generar caché de rutas
php artisan route:cache

# Generar caché de vistas
php artisan view:cache

# Optimizar autoloader
composer install --optimize-autoloader --no-dev

# Establecer permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Configuración de Nginx (Producción)

```nginx
server {
    listen 80;
    server_name dominio.com;
    root /var/www/sicefa/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. RESOLUCIÓN DE PROBLEMAS

### 5.1 Errores comunes

#### Error 1: "SQLSTATE[01000]: Warning: 1265 Data truncated for column 'id'"

**Descripción:** Error al crear notificaciones. El sistema intenta insertar un UUID en una columna de tipo INT.

#### Error 2: "Identifier 'clearSearchBtn' has already been declared"

**Descripción:** Error de JavaScript que indica que una variable está declarada dos veces en el mismo scope.

#### Error 3: "openRequestModal is not defined"

**Descripción:** La función JavaScript no está disponible cuando se ejecuta el evento onclick.

#### Error 4: "No se encontraron insumos" (modal vacío)

**Descripción:** El modal de selección de insumos aparece vacío, sin tarjetas de insumos.

#### Error 5: "El servidor retornó HTML. La petición necesita headers AJAX correctos."

**Descripción:** Una petición AJAX recibe una respuesta HTML en lugar de JSON.

#### Error 6: "403 Forbidden - No tienes permiso para acceder a esta sección"

**Descripción:** El usuario intenta acceder a una sección para la cual no tiene el rol adecuado.

#### Error 7: "CSRF token mismatch"

**Descripción:** El token CSRF ha expirado o no está presente en el formulario.

#### Error 8: Insumos con stock incorrecto

**Descripción:** El stock mostrado no coincide con los movimientos registrados.

---

### 5.2 Posibles causas

| Error | Causas Posibles |
|-------|-----------------|
| **Error 1** | Modelo Notification configurado con UUID pero la tabla usa INT auto-incrementable |
| **Error 2** | Variables JavaScript con el mismo nombre declaradas en diferentes bloques del mismo archivo |
| **Error 3** | La función está definida dentro de `DOMContentLoaded` pero se llama desde `onclick` en el HTML |
| **Error 4** | Los insumos se cargan vía AJAX y la petición falla, o no se pasan al renderizar la vista |
| **Error 5** | El controlador no detecta correctamente el header `X-Requested-With: XMLHttpRequest` |
| **Error 6** | El usuario no tiene el rol correcto asignado o tiene múltiples roles con conflicto |
| **Error 7** | Sesión expirada, caché desactualizada o meta tag CSRF no incluido |
| **Error 8** | Movimientos de almacén eliminados (soft delete) no se excluyen del cálculo |

---

### 5.3 Soluciones

#### Solución Error 1: Notificaciones con UUID

**Archivo:** `Modules/INFRASTOCK/Entities/Notification.php`

```php
// CORRECTO: Usar auto-incremento (INT)
class Notification extends Model
{
    protected $table = 'notifications';
    
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];
}

// INCORRECTO: No usar UUID si la columna es INT
// public $incrementing = false;
// protected $keyType = 'string';
```

#### Solución Error 2: Variables duplicadas

Renombrar las variables para evitar conflictos:

```javascript
// En lugar de:
const clearSearchBtn = document.getElementById('clear-search'); // Duplicado

// Usar nombres específicos:
const clearEquipmentSearchBtn = document.getElementById('clear-search');
const clearRequestSearchBtn = document.getElementById('clear-request-search');
```

#### Solución Error 3: Funciones no disponibles

Definir funciones en scope global antes del HTML que las usa:

```html
<script>
    // Función global disponible inmediatamente
    window.openRequestModal = function() {
        document.getElementById('request-modal').classList.remove('hidden');
    };
</script>

<!-- Ahora el onclick puede usar la función -->
<button onclick="openRequestModal()">Abrir</button>
```

#### Solución Error 4: Modal de insumos vacío

Renderizar los insumos directamente con Blade en lugar de cargarlos vía AJAX:

```blade
{{-- Renderizar directamente con Blade --}}
@foreach($equipments as $equipment)
    <div class="equipment-card" 
         data-equipment-name="{{ $equipment->name }}"
         data-equipment-category="{{ $equipment->category->name ?? 'Sin categoría' }}">
        {{-- Contenido de la tarjeta --}}
    </div>
@endforeach
```

#### Solución Error 5: Respuesta HTML en AJAX

Verificar los headers de la petición:

```javascript
const response = await fetch(url, {
    method: 'GET',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
});
```

En el controlador:

```php
if ($request->ajax() || $request->wantsJson()) {
    return response()->json($data);
}
return redirect()->route('...');
```

#### Solución Error 6: Problema de roles

Verificar los roles del usuario en `usuarios-master.blade.php`:

```php
// Priorizar roles específicos sobre Administrador
$specificRoles = [
    'Aseo' => ['roleName' => 'Personal de Aseo', 'routePrefix' => 'cleaning-staff'],
    'Agroindustria' => ['roleName' => 'Agroindustria', 'routePrefix' => 'agroindustria'],
    // ...
];
```

#### Solución Error 7: CSRF token

Incluir el meta tag en el layout base:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

En formularios AJAX:

```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
fetch(url, {
    headers: { 'X-CSRF-TOKEN': csrfToken }
});
```

#### Solución Error 8: Stock incorrecto

Verificar que los movimientos eliminados se excluyan:

```php
// En Equipment.php - getUsedAmountAttribute()
$entregas = WarehouseMovement::where('equipment_id', $this->id)
    ->where('item_type', 'equipment')
    ->where('role', 'Entrega')
    ->withoutTrashed() // Excluir eliminados
    ->sum('amount');
```

---

## WEBLIOGRAFÍA

| # | Recurso | URL | Descripción |
|---|---------|-----|-------------|
| 1 | Documentación oficial de Laravel | https://laravel.com/docs | Framework PHP principal |
| 2 | Laravel Modules (nwidart) | https://nwidart.com/laravel-modules | Arquitectura modular |
| 3 | Tailwind CSS | https://tailwindcss.com/docs | Framework CSS utility-first |
| 4 | Alpine.js | https://alpinejs.dev/start-here | Framework JavaScript reactivo |
| 5 | Chart.js | https://www.chartjs.org/docs | Librería de gráficos |
| 6 | SweetAlert2 | https://sweetalert2.github.io | Alertas y diálogos |
| 7 | DataTables | https://datatables.net/manual | Tablas interactivas |
| 8 | jQuery | https://api.jquery.com | Librería JavaScript |
| 9 | Font Awesome | https://fontawesome.com/docs | Iconografía |
| 10 | MySQL Documentation | https://dev.mysql.com/doc | Base de datos |
| 11 | PHP Manual | https://www.php.net/manual | Lenguaje PHP |
| 12 | Eloquent ORM | https://laravel.com/docs/eloquent | ORM de Laravel |
| 13 | Blade Templates | https://laravel.com/docs/blade | Motor de plantillas |
| 14 | Laravel Middleware | https://laravel.com/docs/middleware | Middleware HTTP |
| 15 | Laravel Migrations | https://laravel.com/docs/migrations | Migraciones de BD |
| 16 | Composer | https://getcomposer.org/doc | Gestor de dependencias PHP |
| 17 | Node.js | https://nodejs.org/docs | Runtime JavaScript |
| 18 | Git Documentation | https://git-scm.com/doc | Control de versiones |
| 19 | Nginx Documentation | https://nginx.org/en/docs | Servidor web |
| 20 | OWASP Security | https://owasp.org/www-project-web-security-testing-guide | Guía de seguridad web |

---

**Fin del Manual Técnico**  
**Módulo INFRASTOCK - Sistema SICEFA**  
**Versión 1.0 - Febrero 2026**
