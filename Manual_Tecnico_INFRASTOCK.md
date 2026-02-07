# Manual Técnico
## Proyecto: INFRASTOCK

**Análisis y Desarrollo de Software**

Servicio Nacional de Aprendizaje SENA  
Centro de Formación Agroindustrial La Angostura  
Regional Huila

**Abril 2025**

---

## CONTENIDO

1. [Objetivos](#1-objetivos)
   - 1.1 [Objetivos Específicos](#11-objetivos-específicos)
2. [Alcance](#2-alcance)
3. [Requerimientos Técnicos](#3-requerimientos-técnicos)
   - 3.1 [Requerimientos Mínimos de Hardware](#31-requerimientos-mínimos-de-hardware)
   - 3.2 [Requerimientos Mínimos de Software](#32-requerimientos-mínimos-de-software)
4. [Herramientas Utilizadas para el Desarrollo](#4-herramientas-utilizadas-para-el-desarrollo)
5. [Instalación](#5-instalación)
6. [Configuración](#6-configuración)
7. [Diseño de la Arquitectura Física](#7-diseño-de-la-arquitectura-física)
8. [Usuarios](#8-usuarios)
   - 8.1 [Usuarios de Base de Datos](#81-usuarios-de-base-de-datos)
   - 8.2 [Usuarios de Sistemas Operativos](#82-usuarios-de-sistemas-operativos)
   - 8.3 [Usuarios de Aplicaciones](#83-usuarios-de-aplicaciones)
9. [Contingencias y Soluciones](#9-contingencias-y-soluciones)

---

## 1. Objetivos

Este manual técnico tiene como objetivo principal proporcionar la documentación completa y detallada del sistema INFRASTOCK, incluyendo información sobre su arquitectura, instalación, configuración, usuarios y soluciones a problemas comunes. El documento está dirigido a desarrolladores, administradores de sistemas y personal técnico que requiera comprender, instalar, configurar o mantener el sistema.

### 1.1 Objetivos Específicos

• **Documentar la arquitectura del sistema**: Proporcionar una descripción detallada de la estructura física y lógica del sistema INFRASTOCK, incluyendo diagramas de arquitectura y relaciones entre componentes.

• **Especificar requerimientos técnicos**: Detallar los requisitos mínimos de hardware y software necesarios para el correcto funcionamiento del sistema en ambientes de desarrollo, pruebas y producción.

• **Proporcionar guía de instalación**: Describir paso a paso el proceso de instalación del sistema, incluyendo dependencias, configuraciones iniciales y verificaciones post-instalación.

• **Documentar la configuración del sistema**: Explicar todas las configuraciones disponibles, parámetros del sistema, variables de entorno y su propósito.

• **Definir usuarios y permisos**: Especificar los diferentes tipos de usuarios del sistema, sus roles, privilegios y limitaciones de acceso.

• **Proporcionar soluciones a contingencias**: Documentar problemas comunes que pueden ocurrir durante la instalación o uso del sistema, junto con sus respectivas soluciones.

---

## 2. Alcance

Este documento está dirigido a:

• **Desarrolladores de Software**: Personal técnico encargado del desarrollo, mantenimiento y evolución del sistema.

• **Administradores de Sistemas**: Personal responsable de la instalación, configuración y mantenimiento de la infraestructura del sistema.

• **Administradores de Base de Datos**: Personal encargado de la gestión y mantenimiento de las bases de datos del sistema.

• **Personal de Soporte Técnico**: Personal que brinda asistencia técnica a los usuarios finales del sistema.

**Conocimientos básicos requeridos:**

• Conocimientos en programación web (PHP, JavaScript, HTML, CSS)
• Experiencia con frameworks MVC (preferiblemente Laravel)
• Conocimientos básicos de bases de datos relacionales (MySQL)
• Comprensión de conceptos de servidores web (Apache/Nginx)
• Conocimientos básicos de Git para control de versiones
• Familiaridad con sistemas operativos Linux/Windows Server
• Comprensión de conceptos de seguridad web y autenticación

---

## 3. Requerimientos Técnicos

### 3.1 Requerimientos Mínimos de Hardware

**Servidor de Desarrollo:**
- **Procesador**: Intel Core i3 o equivalente (2 núcleos, 2.0 GHz)
- **Memoria RAM (Mínimo)**: 4 GB
- **Disco Duro**: 20 GB de espacio libre
- **Conexión de Red**: Conexión a internet para descarga de dependencias

**Servidor de Producción:**
- **Procesador**: Intel Xeon o equivalente (4 núcleos, 2.4 GHz o superior)
- **Memoria RAM (Mínimo)**: 8 GB (recomendado 16 GB)
- **Disco Duro**: 50 GB de espacio libre (SSD recomendado)
- **Conexión de Red**: Conexión estable a internet con ancho de banda mínimo de 10 Mbps

**Cliente/Estación de Trabajo:**
- **Procesador**: Intel Core i3 o equivalente
- **Memoria RAM**: 4 GB
- **Disco Duro**: 5 GB de espacio libre
- **Navegador Web**: Google Chrome, Mozilla Firefox, Microsoft Edge o Safari (versiones recientes)

### 3.2 Requerimientos Mínimos de Software

**Privilegios de Administrador**: Sí (requerido para instalación y configuración inicial)

**Sistema Operativo del Servidor:**
- Linux (Ubuntu 20.04 LTS o superior, CentOS 7 o superior)
- Windows Server 2016 o superior
- macOS (para desarrollo local)

**Stack Tecnológico:**
- **PHP**: Versión 7.3 o superior (recomendado PHP 8.0+)
- **Composer**: Versión 2.0 o superior (gestor de dependencias de PHP)
- **Base de Datos**: MySQL 5.7 o superior, o MariaDB 10.3 o superior
- **Servidor Web**: Apache 2.4+ con mod_rewrite habilitado, o Nginx 1.18+
- **Node.js**: Versión 14.x o superior (para compilación de assets)
- **NPM**: Versión 6.x o superior (gestor de paquetes de Node.js)

**Extensiones PHP Requeridas:**
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD o Imagick (para procesamiento de imágenes)
- Zip

**Herramientas Adicionales:**
- Git (para control de versiones)
- Editor de código (VS Code, PhpStorm, Sublime Text, etc.)

---

## 4. Herramientas Utilizadas para el Desarrollo

• **Laravel Framework 8.65+**
  - Framework PHP de código abierto basado en el patrón MVC (Modelo-Vista-Controlador)
  - Utilizado para el desarrollo del backend, gestión de rutas, autenticación, y ORM (Eloquent)
  - Proporciona una estructura robusta y escalable para aplicaciones web empresariales

• **Laravel Modules (nwidart/laravel-modules)**
  - Paquete que permite la modularización de aplicaciones Laravel
  - Utilizado para organizar el código del módulo INFRASTOCK de manera independiente y reutilizable
  - Facilita el mantenimiento y la escalabilidad del sistema

• **MySQL/MariaDB**
  - Sistema de gestión de bases de datos relacionales
  - Utilizado para almacenar toda la información del sistema (usuarios, inventarios, solicitudes, etc.)
  - Proporciona integridad referencial y transaccionalidad

• **Tailwind CSS**
  - Framework CSS utility-first
  - Utilizado para el diseño responsive y moderno de la interfaz de usuario
  - Permite un desarrollo rápido de interfaces consistentes

• **Alpine.js**
  - Framework JavaScript ligero y reactivo
  - Utilizado para agregar interactividad a la interfaz sin necesidad de frameworks pesados
  - Implementa funcionalidades como sidebar colapsable, modales, y validaciones en tiempo real

• **JavaScript (Vanilla JS)**
  - Lenguaje de programación del lado del cliente
  - Utilizado para la interactividad, validaciones de formularios, y comunicación AJAX
  - Permite crear experiencias de usuario dinámicas y responsivas

• **DataTables (yajra/laravel-datatables-oracle)**
  - Plugin jQuery para tablas interactivas
  - Utilizado para mostrar datos en tablas con funcionalidades de búsqueda, ordenamiento y paginación
  - Facilita la visualización y gestión de grandes volúmenes de datos

• **SweetAlert2**
  - Biblioteca JavaScript para alertas y notificaciones elegantes
  - Utilizado para confirmaciones de acciones, mensajes de éxito/error, y notificaciones al usuario
  - Mejora la experiencia de usuario con alertas visualmente atractivas

• **Font Awesome**
  - Biblioteca de iconos vectoriales
  - Utilizado para iconografía consistente en toda la aplicación
  - Proporciona más de 1,600 iconos gratuitos

• **Git & GitHub**
  - Sistema de control de versiones distribuido
  - Utilizado para el control de versiones del código fuente, colaboración en equipo, y gestión de cambios
  - Permite rastrear cambios, trabajar en ramas, y mantener un historial completo del proyecto

• **Composer**
  - Gestor de dependencias para PHP
  - Utilizado para instalar y gestionar las librerías y paquetes PHP requeridos por el proyecto
  - Facilita la gestión de dependencias y actualizaciones

• **NPM (Node Package Manager)**
  - Gestor de paquetes para Node.js
  - Utilizado para instalar y gestionar dependencias de frontend (JavaScript, CSS)
  - Permite compilar y optimizar assets del proyecto

• **Laravel Blade**
  - Motor de plantillas de Laravel
  - Utilizado para crear vistas reutilizables y dinámicas
  - Permite la separación de lógica de presentación y código PHP

• **Laravel Eloquent ORM**
  - ORM (Object-Relational Mapping) incluido en Laravel
  - Utilizado para interactuar con la base de datos mediante modelos PHP
  - Facilita las consultas SQL y las relaciones entre tablas

• **Laravel Sanctum**
  - Sistema de autenticación para APIs y SPAs
  - Utilizado para la autenticación de usuarios y gestión de sesiones
  - Proporciona tokens de API seguros

---

## 5. Instalación

### 5.1 Prerrequisitos

Antes de comenzar la instalación, asegúrese de tener instalados y configurados:

1. **PHP 7.3+** con todas las extensiones requeridas
2. **Composer** instalado globalmente
3. **Node.js y NPM** instalados
4. **MySQL/MariaDB** configurado y ejecutándose
5. **Servidor web** (Apache o Nginx) configurado
6. **Git** instalado (si se va a clonar desde repositorio)

### 5.2 Proceso de Instalación

#### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/JhonCelis28/SICEFA.git
cd sicefados
```

#### Paso 2: Instalar Dependencias de PHP

```bash
composer install
```

Este comando instalará todas las dependencias PHP definidas en `composer.json`, incluyendo Laravel y todos los módulos necesarios.

#### Paso 3: Configurar Variables de Entorno

Copie el archivo de ejemplo de variables de entorno:

```bash
cp .env.example .env
```

Edite el archivo `.env` y configure las siguientes variables:

```env
APP_NAME=INFRASTOCK
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_bd
DB_PASSWORD=contraseña_bd
```

#### Paso 4: Generar Clave de Aplicación

```bash
php artisan key:generate
```

#### Paso 5: Crear la Base de Datos

Cree una base de datos MySQL para el proyecto:

```sql
CREATE DATABASE nombre_base_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Paso 6: Ejecutar Migraciones

```bash
php artisan migrate
```

Este comando creará todas las tablas necesarias en la base de datos.

#### Paso 7: Ejecutar Seeders

```bash
php artisan db:seed --class=Modules\\INFRASTOCK\\Database\\Seeders\\INFRASTOCKDatabaseSeeder
```

Este comando poblará la base de datos con datos iniciales (aplicación, roles, categorías, etc.).

#### Paso 8: Instalar Dependencias de Node.js

```bash
npm install
```

#### Paso 9: Compilar Assets

Para desarrollo:
```bash
npm run dev
```

Para producción:
```bash
npm run build
```

#### Paso 10: Configurar Permisos (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Paso 11: Configurar Servidor Web

**Apache (.htaccess ya incluido):**
Asegúrese de que `mod_rewrite` esté habilitado.

**Nginx:**
Configure el servidor para que apunte al directorio `public` del proyecto:

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/al/proyecto/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Paso 12: Verificar Instalación

Acceda a la aplicación en su navegador:
```
http://localhost/infrastock
```

Si todo está correcto, debería ver la página de inicio del módulo INFRASTOCK.

### 5.3 Verificaciones Post-Instalación

1. Verificar que todas las rutas funcionen correctamente
2. Verificar que la conexión a la base de datos esté activa
3. Verificar que los roles y permisos estén configurados
4. Verificar que el almacenamiento de archivos tenga permisos de escritura
5. Verificar que las notificaciones funcionen correctamente

---

## 6. Configuración

### 6.1 Configuración de Base de Datos

El sistema utiliza MySQL/MariaDB como base de datos. La configuración se realiza en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=infrastock_db
DB_USERNAME=infrastock_user
DB_PASSWORD=contraseña_segura
```

### 6.2 Configuración de Aplicación

**Variables principales en `.env`:**

```env
APP_NAME=INFRASTOCK
APP_ENV=production  # o 'local' para desarrollo
APP_KEY=base64:...  # Generado automáticamente
APP_DEBUG=false  # true solo en desarrollo
APP_URL=https://tu-dominio.com
```

### 6.3 Configuración de Sesiones

El sistema utiliza sesiones basadas en archivos por defecto. Para producción, se recomienda usar Redis o base de datos:

```env
SESSION_DRIVER=database  # o 'redis'
SESSION_LIFETIME=120  # minutos
```

### 6.4 Configuración de Cache

Para mejorar el rendimiento en producción:

```env
CACHE_DRIVER=file  # o 'redis' para mejor rendimiento
```

### 6.5 Configuración de Mail

Para envío de notificaciones por correo:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@infrastock.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6.6 Configuración de Almacenamiento

El sistema almacena archivos (imágenes, documentos) en `storage/app/public`. Para hacerlos accesibles:

```bash
php artisan storage:link
```

Esto crea un enlace simbólico desde `public/storage` a `storage/app/public`.

### 6.7 Configuración de Módulos

El módulo INFRASTOCK se configura en `Modules/INFRASTOCK/Config/config.php`:

```php
return [
    'name' => 'INFRASTOCK'
];
```

### 6.8 Comandos Útiles

**Limpiar cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Optimizar para producción:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Ejecutar migraciones:**
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
```

---

## 7. Diseño de la Arquitectura Física

### 7.1 Arquitectura General

El sistema INFRASTOCK está construido sobre una arquitectura modular basada en Laravel, siguiendo el patrón MVC (Modelo-Vista-Controlador). La aplicación está organizada en módulos independientes que se comunican a través de una base de datos compartida y un sistema de autenticación centralizado (SICA).

### 7.2 Componentes Principales

**Frontend (Cliente):**
- Navegador web (Chrome, Firefox, Edge, Safari)
- Interfaz de usuario construida con Blade, Tailwind CSS y Alpine.js
- Comunicación con el backend mediante peticiones HTTP/HTTPS

**Backend (Servidor):**
- Servidor web (Apache/Nginx)
- PHP 7.3+ con Laravel Framework 8.65+
- Módulo INFRASTOCK (nwidart/laravel-modules)

**Base de Datos:**
- MySQL/MariaDB
- Almacenamiento de datos relacionales

**Almacenamiento de Archivos:**
- Sistema de archivos del servidor (`storage/app/public`)
- Almacenamiento de imágenes de productos, herramientas y documentos

### 7.3 Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENTE (Navegador)                    │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   HTML/CSS   │  │  JavaScript  │  │  Alpine.js   │     │
│  │  (Tailwind)  │  │   (Vanilla)  │  │              │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTP/HTTPS
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                    SERVIDOR WEB                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │              Apache/Nginx                           │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │         Laravel Framework 8.65+              │  │   │
│  │  │  ┌────────────────────────────────────────┐ │  │   │
│  │  │  │    Módulo INFRASTOCK                   │ │  │   │
│  │  │  │  ┌──────────┐  ┌──────────┐           │ │  │   │
│  │  │  │  │Controlador│  │  Modelos │           │ │  │   │
│  │  │  │  │          │  │ (Eloquent)│           │ │  │   │
│  │  │  │  └──────────┘  └──────────┘           │ │  │   │
│  │  │  │  ┌──────────┐  ┌──────────┐           │ │  │   │
│  │  │  │  │  Vistas  │  │  Rutas   │           │ │  │   │
│  │  │  │  │  (Blade) │  │          │           │ │  │   │
│  │  │  │  └──────────┘  └──────────┘           │ │  │   │
│  │  │  └────────────────────────────────────────┘ │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────────┬─────────────────────────────────┘
                            │
        ┌───────────────────┴───────────────────┐
        │                                       │
┌───────▼────────┐                  ┌──────────▼──────────┐
│   MySQL/MariaDB│                  │  Sistema de Archivos│
│                │                  │  (storage/app/public)│
│  - Usuarios    │                  │                     │
│  - Roles       │                  │  - Imágenes         │
│  - Inventarios │                  │  - Documentos       │
│  - Solicitudes │                  │                     │
│  - Movimientos │                  │                     │
└────────────────┘                  └─────────────────────┘
```

### 7.4 Estructura de Directorios

```
sicefados/
├── app/                          # Aplicación principal Laravel
├── Modules/
│   └── INFRASTOCK/               # Módulo INFRASTOCK
│       ├── Config/               # Configuración del módulo
│       ├── Database/
│       │   ├── Migrations/       # Migraciones de BD
│       │   └── Seeders/         # Seeders de datos iniciales
│       ├── Entities/            # Modelos Eloquent
│       ├── Http/
│       │   ├── Controllers/     # Controladores
│       │   └── Middleware/      # Middleware personalizado
│       ├── Resources/
│       │   └── views/           # Vistas Blade
│       │       └── layouts/     # Layouts compartidos
│       └── Routes/              # Rutas del módulo
├── public/                      # Punto de entrada público
├── storage/                     # Almacenamiento de archivos
└── .env                        # Variables de entorno
```

### 7.5 Tablas Principales de Base de Datos

- `equipments`: Almacena información de insumos/equipos
- `tools`: Almacena información de herramientas
- `infrastock_categories`: Categorías de productos
- `warehouse_movements`: Movimientos de inventario (solicitudes, préstamos, devoluciones)
- `requests`: Solicitudes de insumos
- `surpluses`: Reportes de sobrantes
- `notifications`: Notificaciones del sistema
- `users`: Usuarios del sistema (compartido con SICA)
- `roles`: Roles de usuarios (compartido con SICA)
- `apps`: Aplicaciones del sistema (compartido con SICA)

### 7.6 Dependencias con Otros Sistemas

**SICA (Sistema de Información y Control de Aprendices):**
- Autenticación centralizada
- Gestión de usuarios y roles
- Información de personas y aprendices

**Puertos TCP/UDP:**
- **Puerto 80/443**: HTTP/HTTPS (servidor web)
- **Puerto 3306**: MySQL/MariaDB (base de datos)
- **Puerto 22**: SSH (administración remota, opcional)

---

## 8. Usuarios

### 8.1 Usuarios de Base de Datos

**Usuario de aplicación:**
- **Nombre del usuario**: `infrastock_user` (o según configuración en `.env`)
- **Descripción/Propósito**: Usuario con privilegios para acceder a la base de datos del módulo INFRASTOCK. Utilizado por la aplicación Laravel para realizar operaciones CRUD.
- **Grupos a los que pertenece**: Usuario estándar de MySQL
- **Privilegios generales a nivel de base de datos**: 
  - `SELECT`, `INSERT`, `UPDATE`, `DELETE` en todas las tablas del módulo INFRASTOCK
  - `CREATE`, `ALTER`, `DROP` en tablas del módulo (solo para migraciones en desarrollo)
- **Privilegios sobre objetos**:
  - Acceso completo a tablas: `equipments`, `tools`, `infrastock_categories`, `warehouse_movements`, `requests`, `surpluses`, `notifications`
  - Acceso de lectura a tablas compartidas: `users`, `roles`, `apps`, `people` (del módulo SICA)

**Usuario de administrador (opcional):**
- **Nombre del usuario**: `root` o `admin_db`
- **Descripción/Propósito**: Usuario administrador de la base de datos, utilizado para tareas de mantenimiento, creación de usuarios, y gestión de la estructura de la base de datos.
- **Grupos a los que pertenece**: Administrador de MySQL
- **Privilegios generales**: Acceso completo a todas las bases de datos y tablas
- **Uso**: Solo para tareas administrativas, no para operaciones de la aplicación

### 8.2 Usuarios de Sistemas Operativos

**Usuario del servidor web:**
- **Nombre del usuario**: `www-data` (Linux) o `apache` (según distribución)
- **Descripción/Propósito**: Usuario bajo el cual se ejecuta el servidor web (Apache/Nginx). Requiere permisos de lectura en el directorio de la aplicación y permisos de escritura en `storage/` y `bootstrap/cache/`.
- **Grupos a los que pertenece**: `www-data` o grupo equivalente
- **Privilegios sobre carpetas**:
  - Lectura: Todo el directorio del proyecto
  - Escritura: `storage/`, `bootstrap/cache/`
  - Ejecución: `public/`

**Usuario de desarrollo (opcional):**
- **Nombre del usuario**: Usuario del desarrollador en el sistema operativo
- **Descripción/Propósito**: Usuario utilizado para desarrollo y despliegue de código
- **Grupos a los que pertenece**: Grupo de usuarios del sistema
- **Privilegios sobre carpetas**: Propietario del directorio del proyecto con permisos completos

### 8.3 Usuarios de Aplicaciones

El sistema INFRASTOCK utiliza un sistema de roles y permisos integrado con el módulo SICA. Los usuarios se autentican a través del sistema centralizado y se les asignan roles específicos dentro del módulo INFRASTOCK.

#### Roles Disponibles:

**1. Administrador**
- **Nombre del usuario**: Usuario con rol "Administrador"
- **Descripción/Propósito**: Control total del sistema INFRASTOCK. Puede gestionar usuarios, aprobar/rechazar solicitudes, gestionar inventarios, y configurar el sistema.
- **Grupos a los que pertenece**: Rol "Administrador" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Gestión completa de usuarios y roles
  - Aprobación/rechazo de solicitudes de insumos
  - Gestión de inventarios (crear, editar, eliminar insumos y herramientas)
  - Gestión de categorías
  - Gestión de áreas productivas y almacenes
  - Visualización de reportes y estadísticas
  - Configuración del sistema

**2. Operario**
- **Nombre del usuario**: Usuario con rol "Operario"
- **Descripción/Propósito**: Personal operativo que puede solicitar insumos y reportar sobrantes.
- **Grupos a los que pertenece**: Rol "Operario" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**3. Personal de Aseo**
- **Nombre del usuario**: Usuario con rol "Aseo"
- **Descripción/Propósito**: Personal de aseo que puede solicitar insumos de limpieza.
- **Grupos a los que pertenece**: Rol "Aseo" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible (limitado a categorías de aseo)
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**4. Ganadería**
- **Nombre del usuario**: Usuario con rol "Ganadería"
- **Descripción/Propósito**: Personal del área de ganadería que puede solicitar insumos relacionados con su área.
- **Grupos a los que pertenece**: Rol "Ganadería" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**5. Centro de Convivencia**
- **Nombre del usuario**: Usuario con rol "Centro de Convivencia"
- **Descripción/Propósito**: Personal del centro de convivencia que puede solicitar insumos.
- **Grupos a los que pertenece**: Rol "Centro de Convivencia" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**6. Vigilancia**
- **Nombre del usuario**: Usuario con rol "Vigilancia"
- **Descripción/Propósito**: Personal de vigilancia que puede solicitar insumos.
- **Grupos a los que pertenece**: Rol "Vigilancia" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**7. Agroindustria**
- **Nombre del usuario**: Usuario con rol "Agroindustria"
- **Descripción/Propósito**: Personal del área de agroindustria que puede solicitar insumos.
- **Grupos a los que pertenece**: Rol "Agroindustria" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**8. Ciencias Básicas**
- **Nombre del usuario**: Usuario con rol "Ciencias Basicas"
- **Descripción/Propósito**: Personal del área de ciencias básicas que puede solicitar insumos.
- **Grupos a los que pertenece**: Rol "Ciencias Basicas" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**9. PSICOLA**
- **Nombre del usuario**: Usuario con rol "Psicola"
- **Descripción/Propósito**: Personal del área PSICOLA que puede solicitar insumos.
- **Grupos a los que pertenece**: Rol "Psicola" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar stock disponible
  - Crear solicitudes de insumos
  - Ver historial de solicitudes
  - Reportar sobrantes
  - Visualizar notificaciones

**10. Instructor**
- **Nombre del usuario**: Usuario con rol "Instructor"
- **Descripción/Propósito**: Instructores que pueden solicitar préstamos de herramientas (no insumos).
- **Grupos a los que pertenece**: Rol "Instructor" en la aplicación INFRASTOCK
- **Privilegios dentro de la aplicación**:
  - Visualizar herramientas disponibles
  - Solicitar préstamos de herramientas
  - Ver historial de préstamos
  - Devolver herramientas
  - Visualizar notificaciones

---

## 9. Contingencias y Soluciones

### 9.1 Problemas Durante la Instalación

**Problema 1: Error "Class not found" o "Composer autoload"**
- **Causa**: Las dependencias de Composer no están instaladas o el autoload no se ha regenerado.
- **Solución**:
  ```bash
  composer install
  composer dump-autoload
  ```

**Problema 2: Error de permisos en `storage/` o `bootstrap/cache/`**
- **Causa**: El servidor web no tiene permisos de escritura en estos directorios.
- **Solución**:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

**Problema 3: Error "APP_KEY not set"**
- **Causa**: La clave de aplicación no ha sido generada.
- **Solución**:
  ```bash
  php artisan key:generate
  ```

**Problema 4: Error de conexión a la base de datos**
- **Causa**: Credenciales incorrectas o base de datos no existe.
- **Solución**:
  1. Verificar que la base de datos existe
  2. Verificar credenciales en `.env`
  3. Verificar que el servidor MySQL está ejecutándose
  4. Probar conexión: `mysql -u usuario -p nombre_bd`

**Problema 5: Error "Route [nombre_ruta] not defined"**
- **Causa**: Las rutas no están cacheadas o el módulo no está registrado correctamente.
- **Solución**:
  ```bash
  php artisan route:clear
  php artisan route:cache
  php artisan module:list  # Verificar que el módulo está registrado
  ```

### 9.2 Problemas Durante el Uso

**Problema 1: Imágenes no se muestran**
- **Causa**: El enlace simbólico de almacenamiento no existe.
- **Solución**:
  ```bash
  php artisan storage:link
  ```

**Problema 2: Error 500 en producción**
- **Causa**: Modo debug activado o errores en logs.
- **Solución**:
  1. Verificar `APP_DEBUG=false` en `.env`
  2. Revisar logs en `storage/logs/laravel.log`
  3. Limpiar cache: `php artisan config:clear`

**Problema 3: Sesiones no persisten**
- **Causa**: Configuración incorrecta de sesiones o permisos.
- **Solución**:
  1. Verificar `SESSION_DRIVER` en `.env`
  2. Verificar permisos en `storage/framework/sessions/`
  3. Limpiar sesiones antiguas: `php artisan session:gc`

**Problema 4: Notificaciones no se envían**
- **Causa**: Configuración incorrecta de correo o servicio no disponible.
- **Solución**:
  1. Verificar configuración de correo en `.env`
  2. Probar envío: `php artisan tinker` y ejecutar `Mail::raw('test', function($msg) { $msg->to('test@example.com')->subject('Test'); });`
  3. Verificar logs en `storage/logs/`

**Problema 5: Sidebar o elementos de UI no funcionan**
- **Causa**: Assets no compilados o cache del navegador.
- **Solución**:
  1. Recompilar assets: `npm run dev` o `npm run build`
  2. Limpiar cache del navegador (Ctrl+Shift+R)
  3. Verificar que Alpine.js está cargado correctamente

**Problema 6: Error "TokenMismatchException"**
- **Causa**: Token CSRF expirado o inválido.
- **Solución**:
  1. Limpiar cache de sesiones
  2. Verificar que `APP_KEY` está configurado correctamente
  3. Aumentar `SESSION_LIFETIME` en `.env` si es necesario

**Problema 7: Migraciones fallan**
- **Causa**: Migraciones ya ejecutadas o conflictos de esquema.
- **Solución**:
  ```bash
  php artisan migrate:status  # Ver estado de migraciones
  php artisan migrate:rollback  # Revertir última migración
  php artisan migrate:fresh --seed  # Reiniciar base de datos (CUIDADO: elimina datos)
  ```

### 9.3 Problemas de Rendimiento

**Problema 1: Carga lenta de páginas**
- **Causa**: Cache no optimizado o consultas ineficientes.
- **Solución**:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
  - Revisar consultas N+1 en modelos
  - Agregar índices en base de datos si es necesario

**Problema 2: Alto uso de memoria**
- **Causa**: Consultas que cargan demasiados datos o cache excesivo.
- **Solución**:
  - Implementar paginación en listados
  - Usar `chunk()` para procesar grandes volúmenes de datos
  - Ajustar `memory_limit` en `php.ini` si es necesario

### 9.4 Problemas de Seguridad

**Problema 1: Archivos accesibles públicamente**
- **Causa**: Configuración incorrecta del servidor web.
- **Solución**:
  - Asegurar que solo `public/` es accesible
  - Verificar que `.env` no es accesible públicamente
  - Configurar correctamente `.htaccess` o configuración de Nginx

**Problema 2: Usuarios no pueden autenticarse**
- **Causa**: Problema con el módulo SICA o configuración de sesiones.
- **Solución**:
  1. Verificar que el módulo SICA está funcionando
  2. Verificar configuración de sesiones
  3. Revisar logs de autenticación

### 9.5 Mantenimiento Preventivo

**Tareas recomendadas periódicamente:**

1. **Backup de base de datos** (diario):
   ```bash
   mysqldump -u usuario -p nombre_bd > backup_$(date +%Y%m%d).sql
   ```

2. **Limpiar logs antiguos** (semanal):
   ```bash
   find storage/logs -name "*.log" -mtime +30 -delete
   ```

3. **Limpiar sesiones antiguas** (diario):
   ```bash
   php artisan session:gc
   ```

4. **Actualizar dependencias** (mensual):
   ```bash
   composer update
   npm update
   ```

5. **Verificar integridad de archivos** (semanal):
   - Verificar permisos de `storage/` y `bootstrap/cache/`
   - Verificar que `storage:link` existe

---

**Fin del Manual Técnico**

---

*Documento generado para el proyecto INFRASTOCK - Versión 1.0*  
*Última actualización: Abril 2025*
