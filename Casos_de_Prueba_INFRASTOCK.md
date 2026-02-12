# DOCUMENTO DE CASOS DE PRUEBA FUNCIONALES

**Proyecto:** Sistema de Gestión de Infraestructura y Stock — Módulo INFRASTOCK  
**Responsable:** Equipo de QA / Desarrollo  
**Fecha:** 11 de febrero de 2026  
**Versión:** 1.0  
**URL Base:** `http://127.0.0.1:8000/infrastock`

---

## Tabla de Contenidos

1. [Objetivo](#1-objetivo)
2. [Alcance](#2-alcance)
3. [Roles del Sistema](#3-roles-del-sistema)
4. [Convenciones](#4-convenciones)
5. [Casos de Prueba — Autenticación y Autorización](#5-casos-de-prueba--autenticación-y-autorización)
6. [Casos de Prueba — Rol Administrador](#6-casos-de-prueba--rol-administrador)
   - 6.1 [Dashboard](#61-dashboard)
   - 6.2 [Gestión de Herramientas (CRUD)](#62-gestión-de-herramientas-crud)
   - 6.3 [Gestión de Insumos (CRUD)](#63-gestión-de-insumos-crud)
   - 6.4 [Gestión de Categorías (CRUD)](#64-gestión-de-categorías-crud)
   - 6.5 [Gestión de Áreas / Unidades Productivas](#65-gestión-de-áreas--unidades-productivas)
   - 6.6 [Préstamos y Devoluciones de Herramientas](#66-préstamos-y-devoluciones-de-herramientas)
   - 6.7 [Solicitudes de Insumos](#67-solicitudes-de-insumos)
   - 6.8 [Devoluciones de Insumos](#68-devoluciones-de-insumos)
   - 6.9 [Gestión de Usuarios](#69-gestión-de-usuarios)
   - 6.10 [Reportes y Exportaciones](#610-reportes-y-exportaciones)
   - 6.11 [Notificaciones (Admin)](#611-notificaciones-admin)
   - 6.12 [Perfil de Usuario (Admin)](#612-perfil-de-usuario-admin)
7. [Casos de Prueba — Rol Instructor](#7-casos-de-prueba--rol-instructor)
   - 7.1 [Dashboard del Instructor](#71-dashboard-del-instructor)
   - 7.2 [Mis Préstamos de Herramientas](#72-mis-préstamos-de-herramientas)
   - 7.3 [Devolución de Herramientas (Instructor)](#73-devolución-de-herramientas-instructor)
   - 7.4 [Notificaciones (Instructor)](#74-notificaciones-instructor)
   - 7.5 [Perfil del Instructor](#75-perfil-del-instructor)
8. [Casos de Prueba — Roles de Áreas / Unidades Productivas](#8-casos-de-prueba--roles-de-áreas--unidades-productivas)
   - 8.1 [Dashboard del Área](#81-dashboard-del-área)
   - 8.2 [Visualización de Stock](#82-visualización-de-stock)
   - 8.3 [Solicitudes de Insumos (Área)](#83-solicitudes-de-insumos-área)
   - 8.4 [Reporte de Sobrantes](#84-reporte-de-sobrantes)
   - 8.5 [Notificaciones (Área)](#85-notificaciones-área)
   - 8.6 [Perfil del Área](#86-perfil-del-área)
9. [Casos de Prueba — Validaciones de Formularios](#9-casos-de-prueba--validaciones-de-formularios)
10. [Casos de Prueba — Navegación e Integración](#10-casos-de-prueba--navegación-e-integración)
11. [Casos de Prueba — Manejo de Errores](#11-casos-de-prueba--manejo-de-errores)
12. [Casos de Prueba — Gestión de Stock](#12-casos-de-prueba--gestión-de-stock)
13. [Matriz Resumen de Casos de Prueba](#13-matriz-resumen-de-casos-de-prueba)

---

## 1. Objetivo

Verificar el correcto funcionamiento de todas las funcionalidades del módulo **INFRASTOCK** del sistema SICEFADOS, asegurando que las operaciones CRUD, flujos de trabajo, validaciones, navegación, gestión de stock, notificaciones y exportaciones operen según los requerimientos establecidos para cada uno de los roles del sistema.

---

## 2. Alcance

Este documento cubre los casos de prueba funcionales para:

- **Autenticación y autorización** por roles.
- **CRUD completo** de Herramientas, Insumos, Categorías, Áreas y Usuarios.
- **Flujos de trabajo** de Préstamos, Devoluciones, Solicitudes de Insumos y Sobrantes.
- **Gestión de stock** (descuento, restauración, validación de disponibilidad).
- **Sistema de notificaciones** (creación, lectura, redirección).
- **Exportación de reportes** (PDF, Excel) por período.
- **Validaciones de formularios** (campos obligatorios, formatos, límites).
- **Navegación y experiencia de usuario** (modales, búsqueda, filtros, paginación).
- **Manejo de errores** (404, 500, validaciones del servidor).

---

## 3. Roles del Sistema

| Rol | Prefijo de Ruta | Descripción |
|-----|-----------------|-------------|
| **Administrador** | `/infrastock/admin/` | Gestión total del módulo: CRUD, aprobaciones, reportes, usuarios. |
| **Instructor** | `/infrastock/instructor/` | Solicitud y devolución de préstamos de herramientas. |
| **Agroindustria** | `/infrastock/agroindustria/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Vigilancia** | `/infrastock/vigilancia/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Ganadería** | `/infrastock/ganaderia/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Psicola** | `/infrastock/psicola/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Ciencias Básicas** | `/infrastock/ciencias-basicas/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Centro de Convivencia** | `/infrastock/centro-convivencia/` | Solicitud de insumos, reporte de sobrantes, visualización de stock. |
| **Personal de Aseo** | `/infrastock/cleaning-staff/` | Solicitud de insumos, reporte de sobrantes, registro propio. |

---

## 4. Convenciones

| Símbolo | Significado |
|---------|-------------|
| **Estado: Exitoso** | La prueba pasó satisfactoriamente. |
| **Estado: Fallido** | La prueba no cumplió el resultado esperado. |
| **Estado: Pendiente** | Aún no ejecutada. |
| **N/A** | No aplica. |

---

## 5. Casos de Prueba — Autenticación y Autorización

### CP-AUTH-001: Inicio de sesión con credenciales válidas (Admin)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-001 |
| **Módulo** | Autenticación |
| **Funcionalidad** | Login del Administrador |
| **Descripción** | Validar que un usuario con rol Administrador pueda iniciar sesión y sea redirigido al Dashboard de administración. |
| **Precondiciones** | El usuario debe estar registrado en la BD con el rol Administrador asignado a la app INFRASTOCK. |
| **Datos de entrada** | Correo: `admin@test.com` — Contraseña: `123456` |
| **Pasos** | 1. Ingresar a la URL del sistema. 2. Digitar el correo electrónico. 3. Digitar la contraseña. 4. Hacer clic en "Entrar". |
| **Resultado Esperado** | Redirección exitosa a `/infrastock/admin/dashboard` con el menú lateral del administrador visible. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-002: Inicio de sesión con credenciales válidas (Instructor)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-002 |
| **Módulo** | Autenticación |
| **Funcionalidad** | Login del Instructor |
| **Descripción** | Validar que un usuario con rol Instructor pueda iniciar sesión y sea redirigido al Dashboard de instructor. |
| **Precondiciones** | El usuario debe estar registrado en la BD con el rol Instructor asignado a la app INFRASTOCK. |
| **Datos de entrada** | Correo: `instructor@test.com` — Contraseña: `123456` |
| **Pasos** | 1. Ingresar a la URL del sistema. 2. Digitar el correo electrónico. 3. Digitar la contraseña. 4. Hacer clic en "Entrar". |
| **Resultado Esperado** | Redirección exitosa a `/infrastock/instructor/dashboard` con el menú lateral del instructor visible. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-003: Inicio de sesión con credenciales válidas (Área / Unidad Productiva)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-003 |
| **Módulo** | Autenticación |
| **Funcionalidad** | Login de Área (Ej: Agroindustria) |
| **Descripción** | Validar que un usuario con rol de área/unidad productiva pueda iniciar sesión y sea redirigido a su Dashboard correspondiente. |
| **Precondiciones** | El usuario debe estar registrado con un rol de unidad productiva (Agroindustria, Vigilancia, etc.) asignado a la app INFRASTOCK. |
| **Datos de entrada** | Correo del usuario del área — Contraseña válida |
| **Pasos** | 1. Ingresar a la URL del sistema. 2. Digitar el correo electrónico. 3. Digitar la contraseña. 4. Hacer clic en "Entrar". |
| **Resultado Esperado** | Redirección exitosa al dashboard del área correspondiente (Ej: `/infrastock/agroindustria/dashboard`). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-004: Inicio de sesión con credenciales inválidas

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-004 |
| **Módulo** | Autenticación |
| **Funcionalidad** | Rechazo de Login |
| **Descripción** | Validar que el sistema rechace credenciales incorrectas y muestre un mensaje de error. |
| **Precondiciones** | Ninguna. |
| **Datos de entrada** | Correo: `falso@test.com` — Contraseña: `clavemal` |
| **Pasos** | 1. Ingresar a la URL del sistema. 2. Digitar un correo no registrado. 3. Digitar una contraseña incorrecta. 4. Hacer clic en "Entrar". |
| **Resultado Esperado** | El sistema muestra un mensaje de error indicando que las credenciales son incorrectas. No se permite el acceso. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-005: Control de acceso — Ruta protegida sin sesión

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-005 |
| **Módulo** | Autorización |
| **Funcionalidad** | Protección de rutas |
| **Descripción** | Verificar que un usuario no autenticado sea redirigido al login al intentar acceder a una ruta protegida. |
| **Precondiciones** | No haber iniciado sesión (o cerrar sesión previamente). |
| **Datos de entrada** | URL directa: `/infrastock/admin/dashboard` |
| **Pasos** | 1. Abrir el navegador sin sesión activa. 2. Escribir directamente la URL protegida en la barra de direcciones. 3. Presionar Enter. |
| **Resultado Esperado** | Redirección automática a la página de Login. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-006: Control de acceso — Instructor accede a rutas de Admin

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-006 |
| **Módulo** | Autorización |
| **Funcionalidad** | Restricción de roles |
| **Descripción** | Verificar que un instructor no pueda acceder a las rutas del administrador. |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | URL: `/infrastock/admin/tools` |
| **Pasos** | 1. Iniciar sesión como Instructor. 2. Escribir manualmente la URL de administración en la barra de direcciones. 3. Presionar Enter. |
| **Resultado Esperado** | El sistema redirige al usuario o muestra un mensaje de acceso no autorizado (403 o redirección). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AUTH-007: Cierre de sesión

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AUTH-007 |
| **Módulo** | Autenticación |
| **Funcionalidad** | Logout |
| **Descripción** | Verificar que al cerrar sesión el usuario sea redirigido al login y no pueda volver con el botón "Atrás". |
| **Precondiciones** | Sesión activa con cualquier rol. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en el menú de usuario (esquina superior derecha). 2. Seleccionar "Cerrar Sesión". 3. Intentar navegar hacia atrás con el botón del navegador. |
| **Resultado Esperado** | Redirección a la página de Login. El botón "Atrás" no permite acceder a vistas protegidas. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 6. Casos de Prueba — Rol Administrador

### 6.1 Dashboard

### CP-ADMIN-001: Visualización del Dashboard del Administrador

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ADMIN-001 |
| **Módulo** | Dashboard — Admin |
| **Funcionalidad** | Carga del Dashboard |
| **Descripción** | Verificar que el Dashboard muestre correctamente las tarjetas de resumen, gráficos y datos estadísticos. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen datos previos en la BD (herramientas, insumos, préstamos). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Iniciar sesión como Administrador. 2. Verificar la página de Dashboard. |
| **Resultado Esperado** | Se visualizan las tarjetas de: Nuevas solicitudes de insumos, Porcentaje de stock, Herramientas en préstamo, Insumos próximos a vencer. Se muestran los gráficos de Consumo por Área y Herramientas por Instructor. Se listan las notificaciones recientes. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.2 Gestión de Herramientas (CRUD)

### CP-TOOL-001: Registrar nueva herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-001 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Crear Herramienta |
| **Descripción** | Verificar que se pueda registrar una nueva herramienta con todos sus campos, incluyendo imagen, y que aparezca en la tabla. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una categoría de tipo "tool". |
| **Datos de entrada** | Nombre: "Taladro Percutor" — Placa: "HER-001" — Imagen: archivo .jpg — Descripción: "Taladro industrial" — Marca: "Bosch" — Modelo: "GSB 550" — Categoría: (seleccionar) — Estado: Disponible — Cantidad Total: 3 — Cantidad Disponible: 3 — Fecha de adquisición: 2025-01-15 |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Hacer clic en "Registrar Herramienta". 3. Llenar todos los campos del formulario. 4. Seleccionar un archivo de imagen. 5. Hacer clic en "Guardar". |
| **Resultado Esperado** | Mensaje de éxito: "Herramienta registrada exitosamente." La herramienta aparece en la tabla con la imagen, nombre, placa, estado, cantidad total, cantidad disponible y fecha de adquisición. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-002: Consultar listado de herramientas

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-002 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Listar Herramientas |
| **Descripción** | Verificar que la tabla de herramientas muestre todos los registros con sus datos correctos. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen herramientas registradas. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Observar la tabla de herramientas. |
| **Resultado Esperado** | Se visualiza la tabla con las columnas: Imagen, Nombre, Placa, Categoría, Estado, Cantidad Total, Cantidad Disponible, Fecha de Adquisición y Acciones (Editar, Eliminar). Los datos coinciden con los registrados en la BD. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-003: Buscar herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-003 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Búsqueda de herramientas |
| **Descripción** | Verificar que la búsqueda filtre herramientas por nombre, placa, marca, modelo, estado o categoría. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen herramientas registradas. |
| **Datos de entrada** | Texto de búsqueda: "Taladro" |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Escribir "Taladro" en el campo de búsqueda. 3. Esperar la actualización de la tabla (o presionar Enter). |
| **Resultado Esperado** | La tabla muestra únicamente las herramientas cuyo nombre, placa, marca, modelo, estado o categoría contengan "Taladro". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-004: Editar herramienta existente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-004 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Editar Herramienta |
| **Descripción** | Verificar que se pueda editar una herramienta existente y que los cambios se reflejen correctamente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una herramienta registrada. |
| **Datos de entrada** | Nombre nuevo: "Taladro Percutor Pro" — Nueva imagen: archivo .png |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Hacer clic en el botón de edición (icono de lápiz) de una herramienta. 3. Modificar el nombre y seleccionar una nueva imagen. 4. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito: "Herramienta actualizada exitosamente." Los datos actualizados se reflejan en la tabla. La nueva imagen se muestra correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-005: Eliminar herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-005 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Eliminar Herramienta |
| **Descripción** | Verificar que se pueda eliminar una herramienta y que desaparezca de la tabla. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una herramienta sin préstamos activos. |
| **Datos de entrada** | Herramienta a eliminar: la creada en CP-TOOL-001. |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Hacer clic en el botón de eliminar (icono de papelera) de la herramienta. 3. Confirmar la eliminación en el diálogo de confirmación. |
| **Resultado Esperado** | Mensaje de éxito: "Herramienta eliminada exitosamente." La herramienta desaparece de la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-006: Exportar herramientas a PDF

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-006 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Exportar a PDF |
| **Descripción** | Verificar que se genere un archivo PDF con el listado completo de herramientas. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen herramientas registradas. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Hacer clic en el botón de exportar a PDF (icono rojo). |
| **Resultado Esperado** | Se descarga o abre un archivo PDF en formato horizontal (landscape) con el inventario completo de herramientas. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-TOOL-007: Exportar herramientas a Excel

| Campo | Detalle |
|-------|---------|
| **ID** | CP-TOOL-007 |
| **Módulo** | Herramientas — Admin |
| **Funcionalidad** | Exportar a Excel |
| **Descripción** | Verificar que se genere un archivo Excel con el listado completo de herramientas. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen herramientas registradas. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Herramientas" en el menú lateral. 2. Hacer clic en el botón de exportar a Excel (icono verde). |
| **Resultado Esperado** | Se descarga un archivo `.xlsx` con el inventario de herramientas. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.3 Gestión de Insumos (CRUD)

### CP-SUPPLY-001: Registrar nuevo insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-001 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Crear Insumo |
| **Descripción** | Verificar que se pueda registrar un nuevo insumo con todos sus campos y que aparezca en la tabla. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una categoría de tipo "supply". |
| **Datos de entrada** | Nombre: "Guantes de Nitrilo" — Categoría: (seleccionar) — Cantidad Inicial: 100 — Stock Mínimo: 20 — Unidad de medida: "Pares" — Fecha de vencimiento: 2027-06-30 |
| **Pasos** | 1. Ir a "Insumos" en el menú lateral. 2. Hacer clic en "Registrar". 3. Llenar todos los campos del formulario. 4. Hacer clic en "Guardar". |
| **Resultado Esperado** | Mensaje de éxito. El insumo aparece en la tabla con nombre, categoría, stock, stock mínimo, unidad de medida y fecha de vencimiento. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-002: Consultar listado de insumos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-002 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Listar Insumos |
| **Descripción** | Verificar que la tabla de insumos muestre todos los registros correctamente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen insumos registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Insumos" en el menú lateral. 2. Observar la tabla de insumos. |
| **Resultado Esperado** | Se visualiza la tabla con los datos de cada insumo. Los insumos próximos a vencer se destacan visualmente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-003: Buscar insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-003 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Búsqueda de insumos |
| **Descripción** | Verificar que la búsqueda filtre insumos por nombre, características, observaciones o categoría. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen insumos registrados. |
| **Datos de entrada** | Texto de búsqueda: "Guantes" |
| **Pasos** | 1. Ir a "Insumos" en el menú lateral. 2. Escribir "Guantes" en el campo de búsqueda. |
| **Resultado Esperado** | La tabla muestra únicamente los insumos que contengan "Guantes" en su nombre, características u observaciones. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-004: Editar insumo existente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-004 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Editar Insumo |
| **Descripción** | Verificar que se pueda editar un insumo y los cambios se persistan. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos un insumo. |
| **Datos de entrada** | Nombre nuevo: "Guantes de Nitrilo XL" — Stock mínimo: 30 |
| **Pasos** | 1. Ir a "Insumos". 2. Hacer clic en "Editar" del insumo. 3. Modificar nombre y stock mínimo. 4. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Los cambios se reflejan en la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-005: Eliminar insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-005 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Eliminar Insumo |
| **Descripción** | Verificar que se pueda eliminar un insumo con confirmación previa. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un insumo sin registros relacionados activos. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Insumos". 2. Hacer clic en "Eliminar" del insumo. 3. Confirmar en el diálogo. |
| **Resultado Esperado** | Mensaje de éxito. El insumo desaparece de la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-006: Exportar insumos a PDF

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-006 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Exportar a PDF |
| **Descripción** | Verificar la generación del reporte PDF de insumos. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen insumos registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Insumos". 2. Hacer clic en el botón de exportar a PDF. |
| **Resultado Esperado** | Se descarga o abre un archivo PDF con el inventario de insumos. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SUPPLY-007: Exportar insumos a Excel

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SUPPLY-007 |
| **Módulo** | Insumos — Admin |
| **Funcionalidad** | Exportar a Excel |
| **Descripción** | Verificar la generación del reporte Excel de insumos. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen insumos registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Insumos". 2. Hacer clic en el botón de exportar a Excel. |
| **Resultado Esperado** | Se descarga un archivo `.xlsx` con el inventario de insumos. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.4 Gestión de Categorías (CRUD)

### CP-CAT-001: Registrar nueva categoría

| Campo | Detalle |
|-------|---------|
| **ID** | CP-CAT-001 |
| **Módulo** | Categorías — Admin |
| **Funcionalidad** | Crear Categoría |
| **Descripción** | Verificar que se pueda crear una categoría de tipo "tool" o "supply". |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Nombre: "Herramientas Eléctricas" — Tipo: "tool" |
| **Pasos** | 1. Ir a "Categorías" en el menú lateral. 2. Hacer clic en "Registrar". 3. Ingresar nombre y seleccionar tipo. 4. Hacer clic en "Guardar". |
| **Resultado Esperado** | Mensaje de éxito. La categoría aparece en la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-CAT-002: Editar categoría

| Campo | Detalle |
|-------|---------|
| **ID** | CP-CAT-002 |
| **Módulo** | Categorías — Admin |
| **Funcionalidad** | Editar Categoría |
| **Descripción** | Verificar la edición de una categoría existente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una categoría. |
| **Datos de entrada** | Nombre nuevo: "Herramientas Eléctricas Industriales" |
| **Pasos** | 1. Ir a "Categorías". 2. Hacer clic en "Editar". 3. Modificar el nombre. 4. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. El nombre actualizado aparece en la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-CAT-003: Eliminar categoría

| Campo | Detalle |
|-------|---------|
| **ID** | CP-CAT-003 |
| **Módulo** | Categorías — Admin |
| **Funcionalidad** | Eliminar Categoría |
| **Descripción** | Verificar que se pueda eliminar una categoría sin herramientas/insumos asociados. |
| **Precondiciones** | Sesión activa con rol Administrador. La categoría no tiene elementos asociados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Categorías". 2. Hacer clic en "Eliminar". 3. Confirmar en el diálogo. |
| **Resultado Esperado** | Mensaje de éxito. La categoría desaparece de la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.5 Gestión de Áreas / Unidades Productivas

### CP-AREA-001: Registrar nueva área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-001 |
| **Módulo** | Áreas — Admin |
| **Funcionalidad** | Crear Área |
| **Descripción** | Verificar el registro de una nueva área/unidad productiva. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Nombre: "Laboratorio de Química" — Descripción: "Área de análisis químico" |
| **Pasos** | 1. Ir a "Áreas" en el menú lateral. 2. Hacer clic en "Registrar". 3. Ingresar datos. 4. Hacer clic en "Guardar". |
| **Resultado Esperado** | Mensaje de éxito. El área aparece en la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-002: Editar área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-002 |
| **Módulo** | Áreas — Admin |
| **Funcionalidad** | Editar Área |
| **Descripción** | Verificar la edición de un área existente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos un área. |
| **Datos de entrada** | Descripción nueva: "Área de análisis químico y bioquímico" |
| **Pasos** | 1. Ir a "Áreas". 2. Hacer clic en "Editar" del área. 3. Modificar la descripción. 4. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Los cambios se reflejan correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-003: Eliminar área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-003 |
| **Módulo** | Áreas — Admin |
| **Funcionalidad** | Eliminar Área |
| **Descripción** | Verificar la eliminación de un área. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Áreas". 2. Hacer clic en "Eliminar". 3. Confirmar. |
| **Resultado Esperado** | Mensaje de éxito. El área desaparece de la tabla. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.6 Préstamos y Devoluciones de Herramientas

### CP-LOAN-001: Registrar préstamo desde el Admin

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-001 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Crear Préstamo (Admin) |
| **Descripción** | Verificar que el administrador pueda registrar un préstamo de herramienta, que el stock se descuente automáticamente, y que las herramientas sin stock aparezcan deshabilitadas. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen herramientas con stock disponible. Existen instructores registrados. |
| **Datos de entrada** | Herramienta: "Taladro Percutor" (Stock: 3/3) — Destinatario: Instructor del sistema — Cantidad: 2 — Observaciones: "Para taller de electrónica" |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones" en el menú lateral. 2. Hacer clic en "Registrar Préstamo". 3. Seleccionar una herramienta del dropdown (verificar que muestra stock). 4. Seleccionar un instructor o ingresar nombre manual. 5. Ingresar cantidad (verificar que no exceda el disponible). 6. Escribir observaciones. 7. Hacer clic en "Registrar Préstamo". |
| **Resultado Esperado** | Mensaje de éxito: "Préstamo registrado exitosamente. Stock actualizado." El préstamo aparece en la tabla con estado "Aprobado". En la vista de herramientas, la cantidad disponible se reduce de 3 a 1. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-002: Validar límite de cantidad en préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-002 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Validación de stock |
| **Descripción** | Verificar que no se pueda solicitar una cantidad mayor a la disponible y que el campo se ajuste automáticamente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una herramienta con cantidad disponible = 2. |
| **Datos de entrada** | Herramienta con stock 2 — Cantidad: 5 (intento de escribir un valor mayor) |
| **Pasos** | 1. Abrir modal de "Registrar Préstamo". 2. Seleccionar la herramienta. 3. Verificar que muestra "Disponible: 2 unidad(es)". 4. Intentar escribir "5" en el campo de cantidad. |
| **Resultado Esperado** | El campo de cantidad se ajusta automáticamente al máximo disponible (2). No permite enviar un valor mayor al stock. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-003: Herramientas sin stock deshabilitadas

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-003 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Deshabilitación de herramientas |
| **Descripción** | Verificar que las herramientas con stock 0 o en mantenimiento aparezcan deshabilitadas en el selector. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una herramienta con stock 0 o en mantenimiento. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Abrir modal de "Registrar Préstamo". 2. Abrir el desplegable de herramientas. 3. Observar las opciones. |
| **Resultado Esperado** | Las herramientas con stock 0 muestran "(Sin stock)" y están deshabilitadas (grises). Las herramientas en mantenimiento muestran "(En mantenimiento)" y están deshabilitadas. Estas opciones no se pueden seleccionar. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-004: Aprobar préstamo de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-004 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Aprobar Préstamo Pendiente |
| **Descripción** | Verificar que al aprobar un préstamo pendiente (creado por un instructor), el stock de la herramienta se descuente y el instructor reciba notificación. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo con estado "Pendiente" creado por un instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar un préstamo con estado "Pendiente". 3. Hacer clic en "Aprobar" (icono de check verde). 4. Confirmar la acción en el diálogo. |
| **Resultado Esperado** | Mensaje de éxito: "Préstamo aprobado exitosamente. Stock actualizado." El estado cambia a "Aprobado". La cantidad disponible de la herramienta se reduce según la cantidad prestada. El instructor recibe una notificación de aprobación. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-005: Rechazar préstamo de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-005 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Rechazar Préstamo Pendiente |
| **Descripción** | Verificar que al rechazar un préstamo pendiente se solicite un motivo y el stock NO se altere. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo con estado "Pendiente". |
| **Datos de entrada** | Motivo de rechazo: "Herramienta reservada para mantenimiento programado" |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar un préstamo con estado "Pendiente". 3. Hacer clic en "Rechazar" (icono X rojo). 4. Ingresar el motivo de rechazo. 5. Confirmar el rechazo. |
| **Resultado Esperado** | Mensaje de éxito: "Préstamo rechazado exitosamente." El estado cambia a "Rechazado". El stock de la herramienta NO se modifica. El motivo se agrega a la descripción del movimiento. El instructor recibe notificación de rechazo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-006: Registrar devolución directa (Admin)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-006 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Registrar Devolución (Admin) |
| **Descripción** | Verificar que el administrador pueda registrar la devolución de una herramienta y el stock se restaure. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo activo (aprobado). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar un préstamo activo con estado "Aprobado". 3. Hacer clic en "Devolver" (icono de retorno). 4. Confirmar la devolución en el diálogo. |
| **Resultado Esperado** | Mensaje de éxito con fecha/hora de devolución. El registro cambia su rol a "Devolución" y queda bloqueado (no editable ni eliminable). El stock de la herramienta se restaura. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-007: Aprobar devolución de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-007 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Aprobar Devolución Pendiente |
| **Descripción** | Verificar que al aprobar una devolución pendiente de un instructor, el stock se restaure y se envíe notificación. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una devolución pendiente creada por un instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar una devolución con estado "Pendiente". 3. (Opcional) Hacer clic en "Ver Descripción" para ver la imagen adjunta. 4. Hacer clic en "Aprobar" (icono de check verde). 5. Confirmar la acción. |
| **Resultado Esperado** | Mensaje de éxito. El estado cambia a "Aprobado". El stock de la herramienta se restaura según la cantidad devuelta. El instructor recibe notificación de aprobación. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-008: Rechazar devolución de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-008 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Rechazar Devolución Pendiente |
| **Descripción** | Verificar que al rechazar una devolución pendiente se solicite un motivo y el stock NO se restaure. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una devolución pendiente. |
| **Datos de entrada** | Motivo de rechazo: "La herramienta presenta daños, requiere revisión" |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar una devolución con estado "Pendiente". 3. Hacer clic en "Rechazar" (icono X rojo). 4. Ingresar el motivo. 5. Confirmar. |
| **Resultado Esperado** | Mensaje de éxito. El estado cambia a "Rechazado". El stock NO se modifica. El instructor recibe notificación de rechazo con el motivo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-009: Ver descripción de devolución con imagen

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-009 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Modal de descripción de devolución |
| **Descripción** | Verificar que el modal de descripción de devolución muestre la imagen adjunta y la descripción del instructor. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una devolución pendiente con imagen adjunta. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar una devolución con estado "Pendiente" que tenga imagen. 3. Hacer clic en "Ver Descripción" (icono de ojo azul). |
| **Resultado Esperado** | Se abre un modal con el nombre de la herramienta, la descripción escrita por el instructor, y la imagen de la devolución cargada correctamente. Los botones "Aprobar" y "Rechazar" están disponibles en el modal. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-010: Eliminar préstamo aprobado (restaurar stock)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-010 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Eliminar Préstamo con restauración de stock |
| **Descripción** | Verificar que al eliminar un préstamo aprobado, el stock se restaure automáticamente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo con estado "Aprobado". |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Localizar un préstamo activo. 3. Hacer clic en "Eliminar". 4. Confirmar la eliminación. |
| **Resultado Esperado** | Mensaje de éxito. El préstamo desaparece de la tabla. El stock de la herramienta se restaura a su valor anterior. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-011: Exportar préstamos a PDF por período

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-011 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Exportar a PDF |
| **Descripción** | Verificar la exportación de préstamos a PDF con filtro por período (mensual, trimestral, anual). |
| **Precondiciones** | Sesión activa con rol Administrador. Existen préstamos registrados. |
| **Datos de entrada** | Período: Mensual — Mes: Febrero 2026 |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Seleccionar el período de exportación. 3. Hacer clic en el botón de exportar a PDF. |
| **Resultado Esperado** | Se descarga un PDF con los préstamos del período seleccionado. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-012: Exportar préstamos a Excel por período

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-012 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Exportar a Excel |
| **Descripción** | Verificar la exportación de préstamos a Excel con filtro por período. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen préstamos registrados. |
| **Datos de entrada** | Período: Trimestral — Trimestre seleccionado |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Seleccionar el período. 3. Hacer clic en exportar a Excel. |
| **Resultado Esperado** | Se descarga un archivo `.xlsx` con los préstamos del período. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-013: Búsqueda de préstamos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-013 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Búsqueda de préstamos |
| **Descripción** | Verificar que la búsqueda filtre préstamos por herramienta, usuario, estado o descripción. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen préstamos registrados. |
| **Datos de entrada** | Texto de búsqueda: "Taladro" |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Escribir "Taladro" en el campo de búsqueda. |
| **Resultado Esperado** | La tabla filtra y muestra solo los préstamos relacionados con herramientas cuyo nombre o placa contengan "Taladro". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-LOAN-014: Estadísticas del módulo de préstamos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-LOAN-014 |
| **Módulo** | Préstamos — Admin |
| **Funcionalidad** | Tarjetas de estadísticas |
| **Descripción** | Verificar que las tarjetas resumen muestren datos correctos. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen préstamos y devoluciones registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Préstamos y Devoluciones". 2. Observar las tarjetas de resumen en la parte superior. |
| **Resultado Esperado** | Se muestran las tarjetas: Total Préstamos, Total Devoluciones, Préstamos Activos, Devoluciones Pendientes, Top 5 herramientas más prestadas y Herramientas en mantenimiento. Los valores son coherentes con los datos de la BD. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.7 Solicitudes de Insumos

### CP-SREQ-001: Visualizar solicitudes de insumos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SREQ-001 |
| **Módulo** | Solicitudes de Insumos — Admin |
| **Funcionalidad** | Listar solicitudes |
| **Descripción** | Verificar que se listen todas las solicitudes de insumos con sus estados (pendiente, aprobada, rechazada). |
| **Precondiciones** | Sesión activa con rol Administrador. Existen solicitudes de insumos de distintas áreas. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Solicitudes de Insumos" en el menú lateral. 2. Observar la tabla de solicitudes. |
| **Resultado Esperado** | Se visualizan las solicitudes con: área solicitante, insumos solicitados, cantidades, estado y fecha. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SREQ-002: Aprobar solicitud de insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SREQ-002 |
| **Módulo** | Solicitudes de Insumos — Admin |
| **Funcionalidad** | Aprobar solicitud |
| **Descripción** | Verificar que al aprobar una solicitud de insumo se cree el movimiento de entrega, se descuente el stock y se notifique al solicitante. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una solicitud con estado "Pendiente". |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Solicitudes de Insumos". 2. Localizar una solicitud pendiente. 3. Hacer clic en "Aprobar". 4. Confirmar la acción. |
| **Resultado Esperado** | Mensaje de éxito. El estado cambia a "Aprobada". Se crea un movimiento de tipo "Entrega" en el almacén. El stock del insumo se descuenta. El solicitante recibe notificación de aprobación. Se genera un registro de sobrante (surplus). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SREQ-003: Rechazar solicitud de insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SREQ-003 |
| **Módulo** | Solicitudes de Insumos — Admin |
| **Funcionalidad** | Rechazar solicitud |
| **Descripción** | Verificar que al rechazar una solicitud el stock NO se altere y se notifique al solicitante. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una solicitud con estado "Pendiente". |
| **Datos de entrada** | Motivo de rechazo (si aplica) |
| **Pasos** | 1. Ir a "Solicitudes de Insumos". 2. Localizar una solicitud pendiente. 3. Hacer clic en "Rechazar". 4. Confirmar o ingresar motivo. |
| **Resultado Esperado** | Mensaje de éxito. El estado cambia a "Rechazada". El stock del insumo NO se modifica. El solicitante recibe notificación de rechazo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SREQ-004: Exportar reporte de consumo a PDF

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SREQ-004 |
| **Módulo** | Solicitudes de Insumos — Admin |
| **Funcionalidad** | Reporte de consumo PDF |
| **Descripción** | Verificar la generación del reporte de consumo de insumos en PDF. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen solicitudes aprobadas. |
| **Datos de entrada** | Período seleccionado |
| **Pasos** | 1. Ir a "Solicitudes de Insumos". 2. Seleccionar período. 3. Hacer clic en exportar a PDF. |
| **Resultado Esperado** | Se descarga un PDF con el reporte de consumo por área y período. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.8 Devoluciones de Insumos

### CP-SRET-001: Visualizar devoluciones de insumos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SRET-001 |
| **Módulo** | Devoluciones de Insumos — Admin |
| **Funcionalidad** | Listar devoluciones |
| **Descripción** | Verificar la visualización de devoluciones de insumos pendientes. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen devoluciones de insumos. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Devoluciones de Insumos" en el menú lateral. 2. Observar el listado. |
| **Resultado Esperado** | Se muestran las devoluciones con estado, insumo, cantidad, área y fecha. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-SRET-002: Aprobar devolución de insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-SRET-002 |
| **Módulo** | Devoluciones de Insumos — Admin |
| **Funcionalidad** | Aprobar devolución de insumo |
| **Descripción** | Verificar que al aprobar una devolución de insumo se cree un movimiento de recepción y se restaure el stock. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe una devolución pendiente de insumo. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Localizar una devolución pendiente de insumo. 2. Hacer clic en "Aprobar". 3. Confirmar. |
| **Resultado Esperado** | Mensaje de éxito. Se crea un movimiento de tipo "Recibe". El stock se actualiza. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.9 Gestión de Usuarios

### CP-USER-001: Registrar nuevo usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CP-USER-001 |
| **Módulo** | Usuarios — Admin |
| **Funcionalidad** | Crear Usuario |
| **Descripción** | Verificar el registro de un nuevo usuario con rol asignado. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Nombre, Email, Rol, Contraseña |
| **Pasos** | 1. Ir a "Usuarios" en el menú lateral. 2. Hacer clic en "Nuevo". 3. Llenar los campos. 4. Guardar. |
| **Resultado Esperado** | Mensaje de éxito. El usuario aparece en la lista con su rol asignado. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-USER-002: Activar/Desactivar usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CP-USER-002 |
| **Módulo** | Usuarios — Admin |
| **Funcionalidad** | Toggle de estado |
| **Descripción** | Verificar que se pueda activar o desactivar un usuario. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos un usuario. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Usuarios". 2. Hacer clic en el botón de activar/desactivar del usuario. |
| **Resultado Esperado** | El estado del usuario cambia (activo/inactivo). Un usuario desactivado no puede iniciar sesión. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.10 Reportes y Exportaciones

### CP-RPT-001: Reporte de consumo por área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-RPT-001 |
| **Módulo** | Reportes — Admin |
| **Funcionalidad** | Consumo por Área |
| **Descripción** | Verificar la generación del reporte de consumo de insumos agrupado por área/unidad productiva. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen movimientos de entrega registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Reportes" > "Consumo por Área". 2. Observar el reporte. 3. (Opcional) Exportar a PDF. |
| **Resultado Esperado** | Se muestra un reporte con el consumo de insumos por cada área. Los datos son coherentes con las entregas registradas. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-RPT-002: Reporte de herramientas por instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-RPT-002 |
| **Módulo** | Reportes — Admin |
| **Funcionalidad** | Herramientas por Instructor |
| **Descripción** | Verificar la generación del reporte de herramientas asignadas por instructor. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen préstamos de herramientas a instructores. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Reportes" > "Herramientas por Instructor". 2. Observar el reporte. 3. (Opcional) Exportar a PDF. |
| **Resultado Esperado** | Se muestra un reporte con las herramientas en préstamo por cada instructor. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.11 Notificaciones (Admin)

### CP-NOTIF-001: Recepción de notificación de nuevo préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NOTIF-001 |
| **Módulo** | Notificaciones — Admin |
| **Funcionalidad** | Notificación de préstamo |
| **Descripción** | Verificar que el admin reciba notificación cuando un instructor registra un nuevo préstamo. |
| **Precondiciones** | Sesión activa con rol Administrador. Un instructor acaba de registrar un préstamo. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar el icono de campana en la barra superior. 2. Hacer clic en la campana. 3. Verificar que aparezca la notificación del nuevo préstamo. |
| **Resultado Esperado** | Se muestra la notificación con el nombre de la herramienta, el instructor y la fecha. El icono de campana muestra el contador de notificaciones sin leer. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NOTIF-002: Marcar notificación como leída y redirigir

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NOTIF-002 |
| **Módulo** | Notificaciones — Admin |
| **Funcionalidad** | Lectura y redirección |
| **Descripción** | Verificar que al hacer clic en una notificación se marque como leída y redirija a la vista correspondiente. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos una notificación sin leer. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en la campana de notificaciones. 2. Hacer clic en una notificación de préstamo. |
| **Resultado Esperado** | La notificación se marca como leída (desaparece del contador). El usuario es redirigido a la vista de "Préstamos y Devoluciones". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NOTIF-003: Notificación de devolución de herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NOTIF-003 |
| **Módulo** | Notificaciones — Admin |
| **Funcionalidad** | Notificación de devolución |
| **Descripción** | Verificar que el admin reciba notificación cuando un instructor registra una devolución de herramienta. |
| **Precondiciones** | Sesión activa con rol Administrador. Un instructor acaba de registrar una devolución. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar la notificación de devolución. |
| **Resultado Esperado** | Se muestra la notificación indicando que el instructor ha devuelto la herramienta, con nombre de la herramienta y descripción. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NOTIF-004: Marcar todas las notificaciones como leídas

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NOTIF-004 |
| **Módulo** | Notificaciones — Admin |
| **Funcionalidad** | Marcar todas como leídas |
| **Descripción** | Verificar que se puedan marcar todas las notificaciones como leídas de una sola vez. |
| **Precondiciones** | Sesión activa con rol Administrador. Existen varias notificaciones sin leer. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en la campana. 2. Hacer clic en "Marcar todas como leídas". |
| **Resultado Esperado** | Todas las notificaciones se marcan como leídas. El contador de la campana se pone en 0. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 6.12 Perfil de Usuario (Admin)

### CP-PROF-001: Editar perfil del administrador

| Campo | Detalle |
|-------|---------|
| **ID** | CP-PROF-001 |
| **Módulo** | Perfil — Admin |
| **Funcionalidad** | Actualizar perfil |
| **Descripción** | Verificar que el administrador pueda editar su perfil (nombre, datos personales). |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Datos actualizados del perfil |
| **Pasos** | 1. Hacer clic en el menú de usuario (esquina superior derecha). 2. Seleccionar "Mi Perfil". 3. Modificar los datos. 4. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Los datos del perfil se actualizan correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 7. Casos de Prueba — Rol Instructor

### 7.1 Dashboard del Instructor

### CP-INST-001: Visualización del Dashboard del Instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-001 |
| **Módulo** | Dashboard — Instructor |
| **Funcionalidad** | Carga del Dashboard |
| **Descripción** | Verificar que el Dashboard del instructor muestre las estadísticas correctas. |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Iniciar sesión como Instructor. 2. Observar el Dashboard. |
| **Resultado Esperado** | Se visualizan: Préstamos activos, Total de préstamos, Préstamos devueltos, Herramienta más prestada, Notificaciones recientes (7 días), Últimos 5 préstamos. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 7.2 Mis Préstamos de Herramientas

### CP-INST-002: Registrar solicitud de préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-002 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Solicitar préstamo |
| **Descripción** | Verificar que el instructor pueda solicitar un préstamo de herramienta, seleccionando solo herramientas con stock disponible. |
| **Precondiciones** | Sesión activa con rol Instructor. Existen herramientas con stock disponible. |
| **Datos de entrada** | Herramienta: (seleccionar del dropdown con stock) — Unidad Productiva: (seleccionar) — Cantidad: 1 — Finalidad: "Para clase de carpintería" — Fecha requerida: (fecha futura) |
| **Pasos** | 1. Ir a "Mis Préstamos" en el menú lateral. 2. Hacer clic en "Registrar Préstamo". 3. Seleccionar herramienta (verificar que muestra stock y deshabilita sin stock). 4. Seleccionar unidad productiva. 5. Ingresar cantidad (verificar límite de stock). 6. Escribir la finalidad. 7. Seleccionar fecha requerida. 8. Hacer clic en "Registrar Préstamo". |
| **Resultado Esperado** | Mensaje de éxito. El préstamo aparece en la tabla con estado "Pendiente". El botón de enviar se deshabilita si no hay stock. Se envía notificación al administrador. El stock de la herramienta NO se descuenta aún (se descuenta al aprobar). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-003: Validación de stock al solicitar préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-003 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Validación de stock en solicitud |
| **Descripción** | Verificar que las herramientas sin stock aparezcan deshabilitadas y que no se pueda solicitar más de lo disponible. |
| **Precondiciones** | Sesión activa con rol Instructor. Existe una herramienta con stock = 0 y otra con stock = 2. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Abrir modal de "Registrar Préstamo". 2. Abrir el desplegable de herramientas. 3. Verificar herramientas con "(Sin stock)" deshabilitadas. 4. Seleccionar herramienta con stock = 2. 5. Verificar que muestra "Disponible: 2 unidad(es)". 6. Intentar ingresar cantidad = 5. |
| **Resultado Esperado** | Las herramientas sin stock están deshabilitadas y en gris. Al seleccionar una herramienta se muestra su estado y stock. La cantidad se ajusta automáticamente al máximo disponible (2). El texto "Máximo disponible: 2 unidad(es)" se muestra debajo del campo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-004: Editar préstamo pendiente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-004 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Editar Préstamo |
| **Descripción** | Verificar que el instructor pueda editar un préstamo que está en estado "Pendiente". |
| **Precondiciones** | Sesión activa con rol Instructor. Existe un préstamo propio con estado "Pendiente". |
| **Datos de entrada** | Finalidad nueva: "Para taller de soldadura" |
| **Pasos** | 1. Ir a "Mis Préstamos". 2. Localizar un préstamo con estado "Pendiente". 3. Hacer clic en "Editar" (icono de lápiz). 4. Modificar la finalidad. 5. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Los datos actualizados se reflejan en la tabla. Solo préstamos "Pendientes" tienen botón de editar. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-005: Eliminar préstamo pendiente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-005 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Eliminar Préstamo |
| **Descripción** | Verificar que el instructor pueda eliminar un préstamo pendiente (no aprobado ni rechazado). |
| **Precondiciones** | Sesión activa con rol Instructor. Existe un préstamo propio con estado "Pendiente". |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Mis Préstamos". 2. Localizar un préstamo con estado "Pendiente". 3. Hacer clic en "Eliminar" (icono de papelera). 4. Confirmar en el diálogo. |
| **Resultado Esperado** | Mensaje de éxito. El préstamo desaparece de la tabla. Solo préstamos "Pendientes" tienen botón de eliminar. El stock de la herramienta NO se altera (nunca fue descontado). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-006: No poder editar/eliminar préstamo aprobado

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-006 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Restricción de acciones |
| **Descripción** | Verificar que un préstamo con estado "Aprobado" o "Rechazado" NO tenga botones de editar ni eliminar. |
| **Precondiciones** | Sesión activa con rol Instructor. Existe un préstamo con estado "Aprobado". |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Mis Préstamos". 2. Localizar un préstamo con estado "Aprobado". 3. Observar los botones de acción. |
| **Resultado Esperado** | Solo se muestra el botón "Devolver" para préstamos aprobados. No aparecen los botones de editar ni eliminar. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 7.3 Devolución de Herramientas (Instructor)

### CP-INST-007: Registrar devolución de herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-007 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Devolver herramienta |
| **Descripción** | Verificar que el instructor pueda registrar la devolución de una herramienta prestada, con imagen adjunta. |
| **Precondiciones** | Sesión activa con rol Instructor. Existe un préstamo propio con estado "Aprobado". |
| **Datos de entrada** | Descripción: "Herramienta devuelta en buen estado" — Imagen: foto del estado actual (archivo .jpg, máx. 10MB) |
| **Pasos** | 1. Ir a "Mis Préstamos". 2. Localizar un préstamo con estado "Aprobado". 3. Hacer clic en "Devolver" (icono de retorno). 4. En el modal de devolución, escribir la descripción del estado. 5. Adjuntar una imagen del estado de la herramienta. 6. Hacer clic en "Registrar Devolución". |
| **Resultado Esperado** | Mensaje de éxito. Se crea un registro de devolución con estado "Pendiente" (espera aprobación del admin). El botón "Devolver" desaparece y muestra una etiqueta "Devolución pendiente" o "Ya devuelto". Se envía notificación al administrador. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-008: Botón "Devolver" oculto tras devolución

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-008 |
| **Módulo** | Mis Préstamos — Instructor |
| **Funcionalidad** | Estado del botón de devolución |
| **Descripción** | Verificar que el botón "Devolver" se oculte cuando ya existe una devolución pendiente o aprobada. |
| **Precondiciones** | Sesión activa con rol Instructor. Existe un préstamo con devolución registrada (pendiente o aprobada). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Mis Préstamos". 2. Localizar el préstamo que ya tiene devolución. 3. Observar la columna de acciones. |
| **Resultado Esperado** | En lugar del botón "Devolver", se muestra una etiqueta con icono de check indicando "Ya devuelto" o "Devolución pendiente". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 7.4 Notificaciones (Instructor)

### CP-INST-009: Recibir notificación de préstamo aprobado

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-009 |
| **Módulo** | Notificaciones — Instructor |
| **Funcionalidad** | Notificación de aprobación |
| **Descripción** | Verificar que el instructor reciba notificación cuando el admin aprueba su préstamo. |
| **Precondiciones** | Sesión activa con rol Instructor. El admin acaba de aprobar un préstamo del instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Hacer clic en la campana. 3. Ver la notificación de aprobación. |
| **Resultado Esperado** | Se muestra notificación: "Préstamo aprobado" con el nombre de la herramienta. Al hacer clic redirige a "Mis Préstamos". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-010: Recibir notificación de préstamo rechazado

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-010 |
| **Módulo** | Notificaciones — Instructor |
| **Funcionalidad** | Notificación de rechazo |
| **Descripción** | Verificar que el instructor reciba notificación con el motivo cuando el admin rechaza su préstamo. |
| **Precondiciones** | Sesión activa con rol Instructor. El admin acaba de rechazar un préstamo del instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar la notificación de rechazo. |
| **Resultado Esperado** | Se muestra notificación: "Préstamo rechazado" con el nombre de la herramienta y el motivo de rechazo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-011: Recibir notificación de devolución aprobada

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-011 |
| **Módulo** | Notificaciones — Instructor |
| **Funcionalidad** | Notificación de devolución aprobada |
| **Descripción** | Verificar que el instructor reciba notificación cuando el admin aprueba su devolución. |
| **Precondiciones** | Sesión activa con rol Instructor. El admin acaba de aprobar una devolución del instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar la notificación. 3. Hacer clic para redirigir. |
| **Resultado Esperado** | Se muestra notificación: "Devolución de herramienta aprobada". Al hacer clic redirige a "Mis Préstamos". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-INST-012: Recibir notificación de devolución rechazada

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-012 |
| **Módulo** | Notificaciones — Instructor |
| **Funcionalidad** | Notificación de devolución rechazada |
| **Descripción** | Verificar que el instructor reciba notificación cuando el admin rechaza su devolución. |
| **Precondiciones** | Sesión activa con rol Instructor. El admin acaba de rechazar una devolución. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar la notificación con el motivo. |
| **Resultado Esperado** | Se muestra notificación: "Devolución de herramienta rechazada" con el motivo. Al hacer clic redirige a "Mis Préstamos". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 7.5 Perfil del Instructor

### CP-INST-013: Editar perfil del instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-INST-013 |
| **Módulo** | Perfil — Instructor |
| **Funcionalidad** | Actualizar perfil |
| **Descripción** | Verificar que el instructor pueda editar su perfil personal. |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | Datos actualizados |
| **Pasos** | 1. Ir a "Mi Perfil" en el menú. 2. Modificar datos. 3. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Datos actualizados correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 8. Casos de Prueba — Roles de Áreas / Unidades Productivas

> **Nota:** Los siguientes casos aplican para todos los roles de áreas/unidades productivas: Agroindustria, Vigilancia, Ganadería, Psicola, Ciencias Básicas, Centro de Convivencia y Personal de Aseo. Se indica el rol genérico "Área" y se debe ejecutar con el rol correspondiente.

### 8.1 Dashboard del Área

### CP-AREA-DASH-001: Visualización del Dashboard del Área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-DASH-001 |
| **Módulo** | Dashboard — Área |
| **Funcionalidad** | Carga del Dashboard |
| **Descripción** | Verificar que el Dashboard del área muestre las estadísticas correspondientes. |
| **Precondiciones** | Sesión activa con rol de área (Ej: Agroindustria). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Iniciar sesión con un usuario del rol de área. 2. Observar el Dashboard. |
| **Resultado Esperado** | Se visualiza el Dashboard con estadísticas propias del área: solicitudes realizadas, estado de solicitudes, datos relevantes. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 8.2 Visualización de Stock

### CP-AREA-STOCK-001: Consultar stock de insumos en tiempo real

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-STOCK-001 |
| **Módulo** | Stock — Área |
| **Funcionalidad** | Visualización de stock |
| **Descripción** | Verificar que el área pueda consultar el stock actual de insumos disponibles. |
| **Precondiciones** | Sesión activa con rol de área. Existen insumos registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Stock" o "Inventario" en el menú lateral. 2. Observar el listado de insumos con cantidades. |
| **Resultado Esperado** | Se visualiza el listado de insumos con nombre, stock actual, unidad de medida y fecha de vencimiento. Los datos coinciden con el stock real del almacén. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-STOCK-002: Ver detalle de insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-STOCK-002 |
| **Módulo** | Stock — Área |
| **Funcionalidad** | Detalle de insumo |
| **Descripción** | Verificar que se pueda ver el detalle completo de un insumo específico. |
| **Precondiciones** | Sesión activa con rol de área. Existen insumos registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Stock". 2. Hacer clic en un insumo específico para ver su detalle. |
| **Resultado Esperado** | Se muestra la información completa del insumo: nombre, categoría, stock, características, fecha de vencimiento, etc. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 8.3 Solicitudes de Insumos (Área)

### CP-AREA-REQ-001: Crear solicitud de insumos

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-REQ-001 |
| **Módulo** | Solicitudes — Área |
| **Funcionalidad** | Crear Solicitud |
| **Descripción** | Verificar que el usuario del área pueda crear una solicitud de insumos con múltiples ítems. |
| **Precondiciones** | Sesión activa con rol de área (Ej: Vigilancia). Existen insumos en el inventario. |
| **Datos de entrada** | Insumo 1: "Guantes de Nitrilo" — Cantidad: 10. Insumo 2: "Desinfectante" — Cantidad: 5. |
| **Pasos** | 1. Ir a "Mis Solicitudes" en el menú lateral. 2. Hacer clic en "Crear Solicitud". 3. Seleccionar los insumos y cantidades requeridas. 4. Hacer clic en "Enviar Solicitud". |
| **Resultado Esperado** | Mensaje de éxito. La solicitud aparece en la lista con estado "Pendiente". Se envía notificación al administrador. Los insumos y cantidades son correctos. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-REQ-002: Consultar mis solicitudes

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-REQ-002 |
| **Módulo** | Solicitudes — Área |
| **Funcionalidad** | Listar mis solicitudes |
| **Descripción** | Verificar que el usuario del área vea solo sus solicitudes con el estado actual. |
| **Precondiciones** | Sesión activa con rol de área. El usuario ha realizado solicitudes previamente. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Mis Solicitudes". 2. Observar la lista de solicitudes. |
| **Resultado Esperado** | Se muestran solo las solicitudes del usuario actual con: fecha, insumos solicitados, cantidades, estado (Pendiente/Aprobada/Rechazada). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-REQ-003: Ver detalle de solicitud

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-REQ-003 |
| **Módulo** | Solicitudes — Área |
| **Funcionalidad** | Detalle de solicitud |
| **Descripción** | Verificar la visualización detallada de una solicitud específica. |
| **Precondiciones** | Sesión activa con rol de área. Existe al menos una solicitud. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Mis Solicitudes". 2. Hacer clic en una solicitud para ver su detalle. |
| **Resultado Esperado** | Se muestra el detalle completo: insumos, cantidades, estado, fecha de solicitud y respuesta (si fue aprobada o rechazada). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-REQ-004: Recibir notificación de solicitud aprobada

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-REQ-004 |
| **Módulo** | Notificaciones — Área |
| **Funcionalidad** | Notificación de aprobación de solicitud |
| **Descripción** | Verificar que el área reciba notificación cuando el admin aprueba su solicitud de insumos. |
| **Precondiciones** | Sesión activa con rol de área. El admin acaba de aprobar una solicitud del área. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar que aparezca la notificación de aprobación. |
| **Resultado Esperado** | Se muestra notificación indicando que la solicitud fue aprobada. Los insumos están disponibles para retiro. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-REQ-005: Recibir notificación de solicitud rechazada

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-REQ-005 |
| **Módulo** | Notificaciones — Área |
| **Funcionalidad** | Notificación de rechazo de solicitud |
| **Descripción** | Verificar que el área reciba notificación cuando el admin rechaza su solicitud. |
| **Precondiciones** | Sesión activa con rol de área. El admin acaba de rechazar una solicitud del área. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Observar la campana de notificaciones. 2. Verificar la notificación de rechazo. |
| **Resultado Esperado** | Se muestra notificación indicando que la solicitud fue rechazada con el motivo (si aplica). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 8.4 Reporte de Sobrantes

### CP-AREA-SUR-001: Registrar sobrante de insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-SUR-001 |
| **Módulo** | Sobrantes — Área |
| **Funcionalidad** | Registrar sobrante |
| **Descripción** | Verificar que el usuario del área pueda reportar un sobrante de insumo al almacén. |
| **Precondiciones** | Sesión activa con rol de área. Existe un insumo que fue previamente entregado y tiene sobrante. |
| **Datos de entrada** | Insumo: (seleccionar) — Cantidad sobrante: 3 |
| **Pasos** | 1. Ir a "Reporte de Sobrantes" en el menú lateral. 2. Hacer clic en "Registrar Sobrante". 3. Seleccionar el insumo y la cantidad sobrante. 4. Hacer clic en "Registrar". |
| **Resultado Esperado** | Mensaje de éxito. El sobrante aparece en la lista. Se envía notificación al administrador. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-SUR-002: Consultar reporte de sobrantes

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-SUR-002 |
| **Módulo** | Sobrantes — Área |
| **Funcionalidad** | Listar sobrantes |
| **Descripción** | Verificar que se listen los sobrantes reportados con sus detalles. |
| **Precondiciones** | Sesión activa con rol de área. Existen sobrantes registrados. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Reporte de Sobrantes". 2. Observar la lista. |
| **Resultado Esperado** | Se visualizan los sobrantes con: insumo, cantidad, fecha de registro y estado. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-SUR-003: Eliminar sobrante

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-SUR-003 |
| **Módulo** | Sobrantes — Área |
| **Funcionalidad** | Eliminar sobrante |
| **Descripción** | Verificar que se pueda eliminar un registro de sobrante. |
| **Precondiciones** | Sesión activa con rol de área. Existe un sobrante registrado. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a "Reporte de Sobrantes". 2. Hacer clic en "Eliminar" del sobrante. 3. Confirmar. |
| **Resultado Esperado** | Mensaje de éxito. El sobrante desaparece de la lista. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 8.5 Notificaciones (Área)

### CP-AREA-NOTIF-001: Visualizar notificaciones del área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-NOTIF-001 |
| **Módulo** | Notificaciones — Área |
| **Funcionalidad** | Listar notificaciones |
| **Descripción** | Verificar que el usuario del área vea sus notificaciones con la información correcta. |
| **Precondiciones** | Sesión activa con rol de área. Existen notificaciones para el usuario. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en la campana de notificaciones. 2. (Opcional) Ir a la vista completa de notificaciones. |
| **Resultado Esperado** | Se muestran las notificaciones con título, mensaje, fecha y estado (leída/no leída). El contador refleja las no leídas. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-AREA-NOTIF-002: Marcar notificación como leída

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-NOTIF-002 |
| **Módulo** | Notificaciones — Área |
| **Funcionalidad** | Marcar como leída |
| **Descripción** | Verificar que se pueda marcar una notificación como leída y que el contador se actualice. |
| **Precondiciones** | Sesión activa con rol de área. Existe una notificación sin leer. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en la campana. 2. Hacer clic en una notificación sin leer. |
| **Resultado Esperado** | La notificación se marca como leída. El contador se reduce en 1. Se redirige a la vista correspondiente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### 8.6 Perfil del Área

### CP-AREA-PROF-001: Editar perfil del usuario de área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-AREA-PROF-001 |
| **Módulo** | Perfil — Área |
| **Funcionalidad** | Actualizar perfil |
| **Descripción** | Verificar que el usuario del área pueda editar su perfil personal. |
| **Precondiciones** | Sesión activa con rol de área. |
| **Datos de entrada** | Datos actualizados del perfil |
| **Pasos** | 1. Ir a "Mi Perfil" en el menú. 2. Modificar datos. 3. Hacer clic en "Actualizar". |
| **Resultado Esperado** | Mensaje de éxito. Datos actualizados correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 9. Casos de Prueba — Validaciones de Formularios

### CP-VAL-001: Campos obligatorios vacíos — Herramienta

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-001 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Campos obligatorios — Crear Herramienta |
| **Descripción** | Verificar que el sistema impida crear una herramienta sin el campo "Nombre" (obligatorio). |
| **Precondiciones** | Sesión activa con rol Administrador. Modal de creación de herramienta abierto. |
| **Datos de entrada** | Nombre: vacío. Demás campos: opcionales. |
| **Pasos** | 1. Abrir modal "Registrar Herramienta". 2. Dejar el campo "Nombre" vacío. 3. Hacer clic en "Guardar". |
| **Resultado Esperado** | Se muestra mensaje de validación: "El campo Nombre es obligatorio." en rojo debajo del campo. El registro NO se crea. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-002: Campos obligatorios vacíos — Insumo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-002 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Campos obligatorios — Crear Insumo |
| **Descripción** | Verificar que el sistema impida crear un insumo sin los campos obligatorios (Nombre, Categoría, Cantidad inicial). |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Todos los campos obligatorios vacíos. |
| **Pasos** | 1. Abrir modal "Registrar Insumo". 2. Dejar campos vacíos. 3. Hacer clic en "Guardar". |
| **Resultado Esperado** | Se muestran mensajes de validación en rojo para cada campo obligatorio faltante. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-003: Campos obligatorios vacíos — Préstamo (Instructor)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-003 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Campos obligatorios — Solicitar Préstamo |
| **Descripción** | Verificar que el sistema impida crear un préstamo sin los campos obligatorios (Herramienta, Unidad Productiva, Finalidad, Fecha requerida). |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | Todos los campos vacíos. |
| **Pasos** | 1. Abrir modal "Registrar Préstamo". 2. Dejar todos los campos vacíos. 3. Hacer clic en "Registrar Préstamo". |
| **Resultado Esperado** | Se muestran mensajes de validación para: Herramienta, Unidad Productiva, Finalidad y Fecha requerida. El botón de enviar está deshabilitado si no se selecciona herramienta. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-004: Formato de imagen inválido

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-004 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Validación de formato de archivo |
| **Descripción** | Verificar que el sistema rechace archivos que no sean imágenes válidas (jpeg, png, jpg, gif). |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | Archivo: documento.pdf (formato no permitido) |
| **Pasos** | 1. Abrir modal de creación/edición de herramienta. 2. Intentar subir un archivo .pdf como imagen. 3. Hacer clic en "Guardar". |
| **Resultado Esperado** | Mensaje de error: "La imagen debe ser de tipo: jpeg, png, jpg o gif." |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-005: Tamaño de imagen excedido

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-005 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Validación de tamaño de archivo |
| **Descripción** | Verificar que el sistema rechace imágenes que excedan 10MB. |
| **Precondiciones** | Sesión activa con cualquier rol que permita subir imágenes. |
| **Datos de entrada** | Archivo: imagen-grande.jpg (> 10MB) |
| **Pasos** | 1. Abrir un formulario con campo de imagen. 2. Intentar subir una imagen de más de 10MB. 3. Enviar el formulario. |
| **Resultado Esperado** | Mensaje de error: "La imagen no puede pesar más de 10MB." |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-006: Fecha requerida en el pasado — Préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-006 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Validación de fecha |
| **Descripción** | Verificar que no se permita seleccionar una fecha pasada como fecha requerida del préstamo. |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | Fecha requerida: 2025-01-01 (fecha pasada) |
| **Pasos** | 1. Abrir modal "Registrar Préstamo". 2. Ingresar una fecha pasada en "Fecha Requerida". 3. Enviar el formulario. |
| **Resultado Esperado** | Mensaje de error: "La fecha requerida debe ser hoy o una fecha futura." |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-007: Cantidad excede stock — Préstamo Admin

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-007 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Validación de stock (servidor) |
| **Descripción** | Verificar que el servidor rechace una cantidad mayor al stock disponible incluso si se fuerza el envío del formulario. |
| **Precondiciones** | Sesión activa con rol Administrador. Herramienta con stock = 1. |
| **Datos de entrada** | Herramienta con stock 1 — Cantidad: 5 (forzado por manipulación del formulario) |
| **Pasos** | 1. Abrir modal de préstamo. 2. Seleccionar herramienta con stock 1. 3. Usando herramientas de desarrollador, forzar el valor de cantidad a 5. 4. Enviar el formulario. |
| **Resultado Esperado** | Mensaje de error del servidor: "No hay stock suficiente de [herramienta]. Disponible: 1, Solicitado: 5." El préstamo NO se crea. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-VAL-008: Motivo de rechazo obligatorio

| Campo | Detalle |
|-------|---------|
| **ID** | CP-VAL-008 |
| **Módulo** | Validaciones |
| **Funcionalidad** | Campo obligatorio — Rechazo de préstamo |
| **Descripción** | Verificar que se requiera un motivo al rechazar un préstamo o devolución. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo pendiente. |
| **Datos de entrada** | Motivo: vacío |
| **Pasos** | 1. Hacer clic en "Rechazar" de un préstamo pendiente. 2. Dejar el campo de motivo vacío. 3. Intentar confirmar. |
| **Resultado Esperado** | Mensaje de validación: "El motivo de rechazo es obligatorio." No se permite el rechazo sin motivo. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 10. Casos de Prueba — Navegación e Integración

### CP-NAV-001: Navegación por menú lateral — Admin

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NAV-001 |
| **Módulo** | Navegación |
| **Funcionalidad** | Menú lateral del Administrador |
| **Descripción** | Verificar que todas las opciones del menú lateral del administrador funcionen correctamente y redirijan a las vistas correspondientes. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en "Dashboard" → Verificar carga. 2. Hacer clic en "Herramientas" → Verificar carga. 3. Hacer clic en "Insumos" → Verificar carga. 4. Hacer clic en "Categorías" → Verificar carga. 5. Hacer clic en "Áreas" → Verificar carga. 6. Hacer clic en "Préstamos y Devoluciones" → Verificar carga. 7. Hacer clic en "Solicitudes de Insumos" → Verificar carga. 8. Hacer clic en "Usuarios" → Verificar carga. 9. Hacer clic en "Reportes" → Verificar submenús. |
| **Resultado Esperado** | Cada opción carga su vista correspondiente sin errores. La transición entre vistas es fluida. La opción activa se resalta en el menú. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla por cada sección_ |
| **Estado** | |

---

### CP-NAV-002: Navegación por menú lateral — Instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NAV-002 |
| **Módulo** | Navegación |
| **Funcionalidad** | Menú lateral del Instructor |
| **Descripción** | Verificar la navegación del menú del instructor. |
| **Precondiciones** | Sesión activa con rol Instructor. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en "Dashboard" → Verificar. 2. Hacer clic en "Mis Préstamos" → Verificar. 3. Hacer clic en "Notificaciones" → Verificar. 4. Hacer clic en "Mi Perfil" → Verificar. |
| **Resultado Esperado** | Cada vista carga correctamente. Solo se muestran las opciones del instructor (no las del admin). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NAV-003: Navegación por menú lateral — Área

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NAV-003 |
| **Módulo** | Navegación |
| **Funcionalidad** | Menú lateral del Área |
| **Descripción** | Verificar la navegación del menú del rol de área/unidad productiva. |
| **Precondiciones** | Sesión activa con rol de área (Ej: Agroindustria). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en "Dashboard" → Verificar. 2. Hacer clic en "Stock / Inventario" → Verificar. 3. Hacer clic en "Mis Solicitudes" → Verificar. 4. Hacer clic en "Reporte de Sobrantes" → Verificar. 5. Hacer clic en "Notificaciones" → Verificar. 6. Hacer clic en "Mi Perfil" → Verificar. |
| **Resultado Esperado** | Cada vista carga correctamente. Solo se muestran las opciones del área. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NAV-004: Paginación de tablas

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NAV-004 |
| **Módulo** | Navegación |
| **Funcionalidad** | Paginación |
| **Descripción** | Verificar que las tablas con muchos registros se paginen correctamente. |
| **Precondiciones** | Sesión activa con cualquier rol. Existen más de 15 registros en alguna tabla (herramientas, préstamos, etc.). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Ir a una vista con tabla paginada. 2. Observar los controles de paginación. 3. Hacer clic en "Siguiente" o un número de página. |
| **Resultado Esperado** | Se muestran 15 registros por página (según configuración). Los controles de paginación funcionan: siguiente, anterior, números de página. Al cambiar de página, los registros se actualizan correctamente. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-NAV-005: Modales de creación y edición

| Campo | Detalle |
|-------|---------|
| **ID** | CP-NAV-005 |
| **Módulo** | Navegación |
| **Funcionalidad** | Modales |
| **Descripción** | Verificar que los modales de creación y edición abren, cierran y se resetean correctamente. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en "Registrar" para abrir modal de creación. 2. Llenar parcialmente. 3. Hacer clic en "Cancelar" o la X. 4. Reabrir el modal. 5. Verificar que los campos estén vacíos (reseteados). |
| **Resultado Esperado** | El modal se abre y cierra correctamente. Al cancelar y reabrir, los campos están en blanco (reseteados). El modal de edición carga los datos de la herramienta seleccionada. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 11. Casos de Prueba — Manejo de Errores

### CP-ERR-001: Página no encontrada (Error 404)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ERR-001 |
| **Módulo** | Sistema |
| **Funcionalidad** | Control de error 404 |
| **Descripción** | Verificar que el sistema muestre una página amigable cuando se accede a una ruta inexistente. |
| **Precondiciones** | Sesión activa con cualquier rol. |
| **Datos de entrada** | URL: `/infrastock/admin/ruta-que-no-existe` |
| **Pasos** | 1. Escribir manualmente una ruta inexistente en el navegador. 2. Presionar Enter. |
| **Resultado Esperado** | Se muestra una página de error 404 amigable (no un error técnico crudo). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-ERR-002: Acceso a recurso inexistente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ERR-002 |
| **Módulo** | Sistema |
| **Funcionalidad** | Control de recurso no encontrado |
| **Descripción** | Verificar el manejo cuando se intenta editar/ver una herramienta con ID inexistente. |
| **Precondiciones** | Sesión activa con rol Administrador. |
| **Datos de entrada** | URL: `/infrastock/admin/tools/99999` (ID que no existe) |
| **Pasos** | 1. Escribir manualmente la URL con un ID inexistente. |
| **Resultado Esperado** | Se muestra un error 404 o se redirige con un mensaje de "Herramienta no encontrada". No se muestra un error técnico. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-ERR-003: Registro duplicado

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ERR-003 |
| **Módulo** | Sistema |
| **Funcionalidad** | Control de duplicados |
| **Descripción** | Verificar que el sistema maneje correctamente el intento de crear un registro duplicado (ej: herramienta con misma placa). |
| **Precondiciones** | Sesión activa con rol Administrador. Ya existe una herramienta con placa "HER-001". |
| **Datos de entrada** | Nombre: "Otra Herramienta" — Placa: "HER-001" (duplicada) |
| **Pasos** | 1. Intentar crear otra herramienta con la misma placa. |
| **Resultado Esperado** | Mensaje de error amigable: "Ya existe una herramienta con estos datos." No se crea el registro duplicado. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-ERR-004: Aprobar préstamo sin stock suficiente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ERR-004 |
| **Módulo** | Sistema |
| **Funcionalidad** | Manejo de error de stock |
| **Descripción** | Verificar que el sistema no permita aprobar un préstamo si el stock ya fue consumido por otro préstamo previo. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe un préstamo pendiente por 3 unidades, pero la herramienta ya solo tiene 1 disponible (otro préstamo fue aprobado mientras este estaba pendiente). |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Intentar aprobar el préstamo pendiente. |
| **Resultado Esperado** | Mensaje de error: "No se puede aprobar: stock insuficiente de [herramienta]. Disponible: 1, Solicitado: 3." El préstamo permanece en estado "Pendiente". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-ERR-005: Diálogo de confirmación antes de eliminar

| Campo | Detalle |
|-------|---------|
| **ID** | CP-ERR-005 |
| **Módulo** | Sistema |
| **Funcionalidad** | Confirmación de eliminación |
| **Descripción** | Verificar que al intentar eliminar un registro se muestre un diálogo de confirmación y que al cancelar no se elimine. |
| **Precondiciones** | Sesión activa con rol Administrador. Existe al menos un registro eliminable. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Hacer clic en "Eliminar" de cualquier registro. 2. En el diálogo de confirmación, hacer clic en "Cancelar". 3. Verificar que el registro sigue visible. |
| **Resultado Esperado** | Se muestra un diálogo de confirmación (SweetAlert). Al cancelar, el registro permanece intacto. Solo al confirmar se ejecuta la eliminación. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

## 12. Casos de Prueba — Gestión de Stock

### CP-STOCK-001: Descuento de stock al crear préstamo (Admin)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-001 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Descuento automático — Préstamo Admin |
| **Descripción** | Verificar que al crear un préstamo desde el admin, la cantidad disponible de la herramienta se descuente correctamente. |
| **Precondiciones** | Sesión activa con rol Administrador. Herramienta "Escuadra" con cantidad_disponible = 3, cantidad_total = 3. |
| **Datos de entrada** | Herramienta: "Escuadra" — Cantidad: 2 |
| **Pasos** | 1. Registrar un préstamo de 2 unidades de "Escuadra". 2. Ir a "Herramientas". 3. Verificar la cantidad disponible de "Escuadra". |
| **Resultado Esperado** | Cantidad disponible = 1 (3 - 2). Si la cantidad disponible llega a 0, el estado de la herramienta cambia a "en_prestamo". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla (antes y después)_ |
| **Estado** | |

---

### CP-STOCK-002: Descuento de stock al aprobar préstamo de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-002 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Descuento automático — Aprobación de préstamo |
| **Descripción** | Verificar que al aprobar un préstamo del instructor, la cantidad disponible se descuente. |
| **Precondiciones** | Existe un préstamo pendiente del instructor por 1 unidad. Herramienta con cantidad_disponible = 3. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Aprobar el préstamo pendiente del instructor. 2. Ir a "Herramientas". 3. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 2 (3 - 1). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla (antes y después)_ |
| **Estado** | |

---

### CP-STOCK-003: NO descontar stock al rechazar préstamo

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-003 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Stock intacto en rechazo |
| **Descripción** | Verificar que al rechazar un préstamo pendiente, el stock de la herramienta NO cambie. |
| **Precondiciones** | Existe un préstamo pendiente. Herramienta con cantidad_disponible = 3. |
| **Datos de entrada** | Motivo de rechazo |
| **Pasos** | 1. Anotar la cantidad disponible actual (3). 2. Rechazar el préstamo pendiente. 3. Verificar la cantidad disponible de la herramienta. |
| **Resultado Esperado** | Cantidad disponible = 3 (sin cambios). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-STOCK-004: Restauración de stock en devolución directa (Admin)

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-004 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Restauración — Devolución directa |
| **Descripción** | Verificar que al registrar una devolución directa desde el admin, el stock se restaure. |
| **Precondiciones** | Existe un préstamo activo de 2 unidades. Herramienta con cantidad_disponible = 1. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Registrar la devolución del préstamo activo. 2. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 3 (1 + 2). El estado vuelve a "disponible". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla (antes y después)_ |
| **Estado** | |

---

### CP-STOCK-005: Restauración de stock al aprobar devolución de instructor

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-005 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Restauración — Aprobación de devolución |
| **Descripción** | Verificar que al aprobar una devolución del instructor, el stock se restaure correctamente. |
| **Precondiciones** | Existe una devolución pendiente de 1 unidad. Herramienta con cantidad_disponible = 2, cantidad_total = 3. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Aprobar la devolución pendiente. 2. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 3 (2 + 1). Estado cambia a "disponible" si todas las unidades están de vuelta. |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla (antes y después)_ |
| **Estado** | |

---

### CP-STOCK-006: NO restaurar stock al rechazar devolución

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-006 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Stock intacto en rechazo de devolución |
| **Descripción** | Verificar que al rechazar una devolución, el stock NO se restaure. |
| **Precondiciones** | Existe una devolución pendiente. Herramienta con cantidad_disponible = 2. |
| **Datos de entrada** | Motivo de rechazo |
| **Pasos** | 1. Anotar cantidad disponible (2). 2. Rechazar la devolución. 3. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 2 (sin cambios). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-STOCK-007: Restauración de stock al eliminar préstamo aprobado

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-007 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Restauración — Eliminación de préstamo |
| **Descripción** | Verificar que al eliminar un préstamo aprobado, el stock se restaure. |
| **Precondiciones** | Existe un préstamo aprobado de 1 unidad. Herramienta con cantidad_disponible = 2. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Eliminar el préstamo aprobado. 2. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 3 (2 + 1). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-STOCK-008: NO restaurar stock al eliminar préstamo pendiente

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-008 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Stock intacto — Eliminación de préstamo pendiente |
| **Descripción** | Verificar que al eliminar un préstamo que estaba en estado "Pendiente", el stock NO cambie (nunca fue descontado). |
| **Precondiciones** | Existe un préstamo pendiente. Herramienta con cantidad_disponible = 3. |
| **Datos de entrada** | N/A |
| **Pasos** | 1. Anotar cantidad disponible (3). 2. Eliminar el préstamo pendiente. 3. Verificar la cantidad disponible. |
| **Resultado Esperado** | Cantidad disponible = 3 (sin cambios). |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla_ |
| **Estado** | |

---

### CP-STOCK-009: Cambio de estado de herramienta según disponibilidad

| Campo | Detalle |
|-------|---------|
| **ID** | CP-STOCK-009 |
| **Módulo** | Gestión de Stock |
| **Funcionalidad** | Estado dinámico de herramienta |
| **Descripción** | Verificar que el estado de la herramienta cambie automáticamente según la cantidad disponible. |
| **Precondiciones** | Herramienta con cantidad_total = 1, cantidad_disponible = 1, estado = "disponible". |
| **Datos de entrada** | Préstamo de 1 unidad. |
| **Pasos** | 1. Registrar préstamo de 1 unidad. 2. Verificar estado → "en_prestamo". 3. Registrar devolución. 4. Verificar estado → "disponible". |
| **Resultado Esperado** | Al llegar a 0 disponible: estado = "en_prestamo". Al restaurar a cantidad_total: estado = "disponible". |
| **Resultado Obtenido** | |
| **Evidencia** | _Captura de pantalla (cada paso)_ |
| **Estado** | |

---

## 13. Matriz Resumen de Casos de Prueba

| ID | Módulo | Rol | Funcionalidad | Resultado Esperado | Estado |
|----|--------|-----|---------------|-------------------|--------|
| CP-AUTH-001 | Autenticación | Admin | Login válido | Redirección a Dashboard Admin | |
| CP-AUTH-002 | Autenticación | Instructor | Login válido | Redirección a Dashboard Instructor | |
| CP-AUTH-003 | Autenticación | Área | Login válido | Redirección a Dashboard del Área | |
| CP-AUTH-004 | Autenticación | Todos | Login inválido | Mensaje de error | |
| CP-AUTH-005 | Autorización | Todos | Ruta protegida sin sesión | Redirección al Login | |
| CP-AUTH-006 | Autorización | Instructor | Acceso a rutas Admin | Acceso denegado | |
| CP-AUTH-007 | Autenticación | Todos | Logout | Redirección al Login | |
| CP-ADMIN-001 | Dashboard | Admin | Carga del Dashboard | Datos y gráficos visibles | |
| CP-TOOL-001 | Herramientas | Admin | Crear herramienta | Registro guardado con imagen | |
| CP-TOOL-002 | Herramientas | Admin | Listar herramientas | Tabla con datos correctos | |
| CP-TOOL-003 | Herramientas | Admin | Buscar herramienta | Filtro funcional | |
| CP-TOOL-004 | Herramientas | Admin | Editar herramienta | Cambios persistidos | |
| CP-TOOL-005 | Herramientas | Admin | Eliminar herramienta | Registro eliminado | |
| CP-TOOL-006 | Herramientas | Admin | Exportar PDF | Descarga PDF generado | |
| CP-TOOL-007 | Herramientas | Admin | Exportar Excel | Descarga XLSX generado | |
| CP-SUPPLY-001 | Insumos | Admin | Crear insumo | Registro guardado | |
| CP-SUPPLY-002 | Insumos | Admin | Listar insumos | Tabla con datos correctos | |
| CP-SUPPLY-003 | Insumos | Admin | Buscar insumo | Filtro funcional | |
| CP-SUPPLY-004 | Insumos | Admin | Editar insumo | Cambios persistidos | |
| CP-SUPPLY-005 | Insumos | Admin | Eliminar insumo | Registro eliminado | |
| CP-SUPPLY-006 | Insumos | Admin | Exportar PDF | Descarga PDF generado | |
| CP-SUPPLY-007 | Insumos | Admin | Exportar Excel | Descarga XLSX generado | |
| CP-CAT-001 | Categorías | Admin | Crear categoría | Registro guardado | |
| CP-CAT-002 | Categorías | Admin | Editar categoría | Cambios persistidos | |
| CP-CAT-003 | Categorías | Admin | Eliminar categoría | Registro eliminado | |
| CP-AREA-001 | Áreas | Admin | Crear área | Registro guardado | |
| CP-AREA-002 | Áreas | Admin | Editar área | Cambios persistidos | |
| CP-AREA-003 | Áreas | Admin | Eliminar área | Registro eliminado | |
| CP-LOAN-001 | Préstamos | Admin | Crear préstamo | Stock descontado, registro creado | |
| CP-LOAN-002 | Préstamos | Admin | Validar límite cantidad | Campo ajustado al máximo | |
| CP-LOAN-003 | Préstamos | Admin | Herramientas sin stock | Opciones deshabilitadas | |
| CP-LOAN-004 | Préstamos | Admin | Aprobar préstamo | Stock descontado, notificación enviada | |
| CP-LOAN-005 | Préstamos | Admin | Rechazar préstamo | Stock intacto, notificación con motivo | |
| CP-LOAN-006 | Préstamos | Admin | Devolución directa | Stock restaurado, registro bloqueado | |
| CP-LOAN-007 | Préstamos | Admin | Aprobar devolución | Stock restaurado, notificación enviada | |
| CP-LOAN-008 | Préstamos | Admin | Rechazar devolución | Stock intacto, motivo registrado | |
| CP-LOAN-009 | Préstamos | Admin | Modal descripción devolución | Imagen y descripción visibles | |
| CP-LOAN-010 | Préstamos | Admin | Eliminar préstamo aprobado | Stock restaurado | |
| CP-LOAN-011 | Préstamos | Admin | Exportar PDF período | PDF descargado | |
| CP-LOAN-012 | Préstamos | Admin | Exportar Excel período | XLSX descargado | |
| CP-LOAN-013 | Préstamos | Admin | Buscar préstamos | Filtro funcional | |
| CP-LOAN-014 | Préstamos | Admin | Estadísticas | Tarjetas con datos correctos | |
| CP-SREQ-001 | Solicitudes | Admin | Listar solicitudes | Tabla con datos y estados | |
| CP-SREQ-002 | Solicitudes | Admin | Aprobar solicitud | Stock descontado, notificación | |
| CP-SREQ-003 | Solicitudes | Admin | Rechazar solicitud | Stock intacto, notificación | |
| CP-SREQ-004 | Solicitudes | Admin | Reporte consumo PDF | PDF descargado | |
| CP-SRET-001 | Devoluciones Insumos | Admin | Listar devoluciones | Tabla con datos | |
| CP-SRET-002 | Devoluciones Insumos | Admin | Aprobar devolución | Movimiento de recepción creado | |
| CP-USER-001 | Usuarios | Admin | Crear usuario | Registro guardado con rol | |
| CP-USER-002 | Usuarios | Admin | Activar/Desactivar | Estado cambiado | |
| CP-RPT-001 | Reportes | Admin | Consumo por área | Reporte generado | |
| CP-RPT-002 | Reportes | Admin | Herramientas por instructor | Reporte generado | |
| CP-NOTIF-001 | Notificaciones | Admin | Notif. nuevo préstamo | Notificación recibida | |
| CP-NOTIF-002 | Notificaciones | Admin | Leer y redirigir | Marcada como leída, redirección | |
| CP-NOTIF-003 | Notificaciones | Admin | Notif. devolución | Notificación recibida | |
| CP-NOTIF-004 | Notificaciones | Admin | Marcar todas leídas | Contador en 0 | |
| CP-PROF-001 | Perfil | Admin | Editar perfil | Datos actualizados | |
| CP-INST-001 | Dashboard | Instructor | Carga Dashboard | Estadísticas visibles | |
| CP-INST-002 | Préstamos | Instructor | Solicitar préstamo | Estado pendiente, notificación | |
| CP-INST-003 | Préstamos | Instructor | Validación stock | Herramientas sin stock deshabilitadas | |
| CP-INST-004 | Préstamos | Instructor | Editar préstamo pendiente | Cambios persistidos | |
| CP-INST-005 | Préstamos | Instructor | Eliminar préstamo pendiente | Registro eliminado, stock intacto | |
| CP-INST-006 | Préstamos | Instructor | Restricción acciones | Sin editar/eliminar aprobados | |
| CP-INST-007 | Devolución | Instructor | Devolver herramienta | Estado pendiente, notificación admin | |
| CP-INST-008 | Devolución | Instructor | Botón oculto tras devolución | Muestra "Ya devuelto" | |
| CP-INST-009 | Notificaciones | Instructor | Notif. aprobación préstamo | Notificación recibida | |
| CP-INST-010 | Notificaciones | Instructor | Notif. rechazo préstamo | Notificación con motivo | |
| CP-INST-011 | Notificaciones | Instructor | Notif. devolución aprobada | Notificación recibida | |
| CP-INST-012 | Notificaciones | Instructor | Notif. devolución rechazada | Notificación con motivo | |
| CP-INST-013 | Perfil | Instructor | Editar perfil | Datos actualizados | |
| CP-AREA-DASH-001 | Dashboard | Área | Carga Dashboard | Estadísticas del área | |
| CP-AREA-STOCK-001 | Stock | Área | Consultar stock | Listado con cantidades | |
| CP-AREA-STOCK-002 | Stock | Área | Detalle de insumo | Información completa | |
| CP-AREA-REQ-001 | Solicitudes | Área | Crear solicitud | Estado pendiente, notificación admin | |
| CP-AREA-REQ-002 | Solicitudes | Área | Consultar solicitudes | Solo mis solicitudes | |
| CP-AREA-REQ-003 | Solicitudes | Área | Ver detalle solicitud | Información completa | |
| CP-AREA-REQ-004 | Notificaciones | Área | Notif. aprobación solicitud | Notificación recibida | |
| CP-AREA-REQ-005 | Notificaciones | Área | Notif. rechazo solicitud | Notificación con motivo | |
| CP-AREA-SUR-001 | Sobrantes | Área | Registrar sobrante | Registro creado, notificación | |
| CP-AREA-SUR-002 | Sobrantes | Área | Consultar sobrantes | Listado con datos | |
| CP-AREA-SUR-003 | Sobrantes | Área | Eliminar sobrante | Registro eliminado | |
| CP-AREA-NOTIF-001 | Notificaciones | Área | Ver notificaciones | Listado correcto | |
| CP-AREA-NOTIF-002 | Notificaciones | Área | Marcar como leída | Contador actualizado | |
| CP-AREA-PROF-001 | Perfil | Área | Editar perfil | Datos actualizados | |
| CP-VAL-001 | Validaciones | Admin | Campos obligatorios herramienta | Mensajes de error | |
| CP-VAL-002 | Validaciones | Admin | Campos obligatorios insumo | Mensajes de error | |
| CP-VAL-003 | Validaciones | Instructor | Campos obligatorios préstamo | Mensajes de error | |
| CP-VAL-004 | Validaciones | Todos | Formato imagen inválido | Mensaje de formato no permitido | |
| CP-VAL-005 | Validaciones | Todos | Tamaño imagen excedido | Mensaje de exceso de tamaño | |
| CP-VAL-006 | Validaciones | Instructor | Fecha pasada | Mensaje de fecha inválida | |
| CP-VAL-007 | Validaciones | Admin | Cantidad excede stock (servidor) | Mensaje de stock insuficiente | |
| CP-VAL-008 | Validaciones | Admin | Motivo rechazo obligatorio | Mensaje de campo requerido | |
| CP-NAV-001 | Navegación | Admin | Menú lateral completo | Todas las vistas cargan | |
| CP-NAV-002 | Navegación | Instructor | Menú lateral completo | Todas las vistas cargan | |
| CP-NAV-003 | Navegación | Área | Menú lateral completo | Todas las vistas cargan | |
| CP-NAV-004 | Navegación | Todos | Paginación | Navegación entre páginas | |
| CP-NAV-005 | Navegación | Todos | Modales | Abrir, cerrar, resetear | |
| CP-ERR-001 | Errores | Todos | Error 404 | Página amigable | |
| CP-ERR-002 | Errores | Admin | Recurso inexistente | Manejo adecuado | |
| CP-ERR-003 | Errores | Admin | Registro duplicado | Mensaje de error | |
| CP-ERR-004 | Errores | Admin | Aprobar sin stock | Mensaje de stock insuficiente | |
| CP-ERR-005 | Errores | Todos | Confirmación de eliminación | Diálogo SweetAlert | |
| CP-STOCK-001 | Stock | Admin | Descuento préstamo admin | cantidad_disponible - amount | |
| CP-STOCK-002 | Stock | Admin | Descuento aprobación instructor | cantidad_disponible - amount | |
| CP-STOCK-003 | Stock | Admin | No descuento en rechazo | Stock sin cambios | |
| CP-STOCK-004 | Stock | Admin | Restauración devolución directa | cantidad_disponible + amount | |
| CP-STOCK-005 | Stock | Admin | Restauración devolución instructor | cantidad_disponible + amount | |
| CP-STOCK-006 | Stock | Admin | No restaurar en rechazo devolución | Stock sin cambios | |
| CP-STOCK-007 | Stock | Admin | Restauración al eliminar aprobado | cantidad_disponible + amount | |
| CP-STOCK-008 | Stock | Admin | No restaurar al eliminar pendiente | Stock sin cambios | |
| CP-STOCK-009 | Stock | Admin | Estado dinámico herramienta | disponible ↔ en_prestamo | |

---

> **Total de Casos de Prueba: 89**

---

*Documento generado para el módulo INFRASTOCK del sistema SICEFADOS.*  
*Fecha de elaboración: 11 de febrero de 2026.*
