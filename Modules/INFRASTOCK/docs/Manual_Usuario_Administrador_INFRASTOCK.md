# MANUAL DE USUARIO

## MODULO ADMINISTRACION DE INVENTARIO E INFRAESTRUCTURA

## APLICATIVO WEB SISTEMA DE INFORMACION DEL CENTRO DE FORMACION AGROPECUARIA

## INFRASTOCK

## V1.0

### CENTRO DE FORMACION AGROPECUARIA

### BOGOTA, FEBRERO DE 2026

---

| | MANUAL DE USUARIO |
|---|---|
| **Modulo:** | Administracion de Inventario - INFRASTOCK |
| **Codigo:** | INFRASTOCK-MU-001 |
| **Version:** | 1.0 |
| **Paginas:** | 1 de 45 |

---

## TABLA DE CONTENIDO

1. [OBJETIVO](#1-objetivo)
2. [ALCANCE](#2-alcance)
3. [AMBITO DE APLICACION](#3-ambito-de-aplicacion)
4. [NORMATIVA Y OTROS DOCUMENTOS EXTERNOS](#4-normativa-y-otros-documentos-externos)
5. [DEFINICIONES](#5-definiciones)
6. [REQUISITOS TECNOLOGICOS PARA ACCEDER AL APLICATIVO](#6-requisitos-tecnologicos-para-acceder-al-aplicativo)
7. [FUNCIONES DE LOS ROLES DEL SISTEMA](#7-funciones-de-los-roles-del-sistema)
8. [FUNCIONALIDADES MODULO ADMINISTRACION DE INVENTARIO](#8-funcionalidades-modulo-administracion-de-inventario)
9. [INGRESO AL MODULO INFRASTOCK](#9-ingreso-al-modulo-infrastock)
10. [OPERACION DEL MODULO ADMINISTRACION DE INVENTARIO](#10-operacion-del-modulo-administracion-de-inventario)
11. [MENSAJES DE ADVERTENCIA DEL MODULO](#11-mensajes-de-advertencia-del-modulo)

---

## 1. OBJETIVO

Indicar el procedimiento de ingreso, autenticacion, gestion de inventario, aprobacion y rechazo de solicitudes, control de prestamos y devoluciones, administracion de usuarios y generacion de reportes, en el Modulo de Administracion de Inventario e Infraestructura del aplicativo Web, Sistema de Informacion del Centro de Formacion Agropecuaria – SICEFA, denominado **INFRASTOCK**.

---

## 2. ALCANCE

Inicia con el acceso al sistema mediante autenticacion del usuario administrador a traves del sistema SICA, continua con la gestion integral del inventario de insumos y herramientas, la revision y procesamiento de solicitudes, el control de prestamos y devoluciones, la administracion de usuarios y roles, y termina con la generacion de reportes y el cierre de sesion del usuario.

---

## 3. AMBITO DE APLICACION

Las disposiciones contenidas en este Manual aplican al **Administrador del Sistema INFRASTOCK**, quien es el responsable de asegurar la correcta gestion del inventario de insumos y herramientas del Centro de Formacion Agropecuaria, incluyendo:

- La administracion del catalogo de insumos y herramientas.
- La aprobacion o rechazo de solicitudes de materiales realizadas por los usuarios de las diferentes areas productivas.
- El control de prestamos y devoluciones de herramientas.
- La gestion de reportes de sobrantes (insumos no utilizados).
- La administracion de usuarios, roles y permisos del sistema.
- El monitoreo del stock, alertas de vencimiento y notificaciones del sistema.

---

## 4. NORMATIVA Y OTROS DOCUMENTOS EXTERNOS

- Politica interna de gestion de activos e inventario del Centro de Formacion Agropecuaria.
- Procedimiento de solicitud y entrega de materiales e insumos.
- Procedimiento de prestamo y devolucion de herramientas.
- Reglamento interno para el uso de herramientas e implementos del centro.
- Ley 1581 de 2012 – "Por la cual se dictan disposiciones generales para la Proteccion de Datos Personales".
- Decreto 1377 de 2013 – "Por el cual se reglamenta parcialmente la Ley 1581 de 2012".
- Normativa vigente del SENA aplicable a la gestion de inventario en centros de formacion.

---

## 5. DEFINICIONES

- **INFRASTOCK:** Modulo de Gestion de Inventario e Infraestructura del sistema SICEFA, desarrollado para el control y seguimiento de insumos, herramientas, solicitudes, prestamos y devoluciones del Centro de Formacion Agropecuaria.

- **SICEFA:** Sistema de Informacion del Centro de Formacion Agropecuaria. Plataforma web que integra multiples modulos de gestion para el centro educativo.

- **SICA:** Sistema de autenticacion centralizado utilizado para el inicio de sesion y la validacion de credenciales de los usuarios del sistema SICEFA.

- **Administrador:** Usuario con permisos de acceso total al modulo INFRASTOCK, responsable de la gestion del inventario, aprobacion de solicitudes, control de prestamos y administracion de usuarios.

- **Insumo (Equipment):** Material consumible registrado en el inventario del almacen, que se entrega a las areas productivas mediante solicitudes aprobadas. Ejemplos: abrazaderas, tornillos, cables, productos de limpieza.

- **Herramienta (Tool):** Equipo o utensilio no consumible que se presta temporalmente a los usuarios y debe ser devuelto al almacen. Ejemplos: taladro, martillo, sierra electrica.

- **Solicitud de Insumos (Request):** Peticion formal realizada por un usuario de area productiva para obtener insumos del almacen. Requiere aprobacion del administrador.

- **Prestamo:** Entrega temporal de una herramienta a un usuario (generalmente instructor) con compromiso de devolucion en condiciones adecuadas.

- **Devolucion:** Acto de retorno de una herramienta prestada o de insumos sobrantes al almacen.

- **Sobrante (Surplus):** Cantidad de insumo entregado a un usuario que no fue utilizada completamente y se reporta para devolucion al inventario del almacen.

- **Area Productiva (Productive Unit):** Unidad organizacional del Centro de Formacion Agropecuaria. Ejemplos: Ganaderia, Agroindustria, Ciencias Basicas.

- **Almacen (Warehouse):** Espacio fisico donde se almacenan los insumos y herramientas del centro.

- **Categoria (Category):** Clasificacion utilizada para organizar los insumos y herramientas del inventario. Pueden ser de tipo "supply" (insumo) o "tool" (herramienta).

- **Stock:** Cantidad disponible de un insumo o herramienta en el almacen en un momento determinado.

- **Movimiento de Almacen (Warehouse Movement):** Registro de toda transaccion que afecta el inventario, ya sea de entrada (Recibe), salida (Entrega), solicitud, prestamo o devolucion.

- **Dashboard:** Panel principal del administrador que muestra indicadores, estadisticas y resumen del estado general del sistema.

- **Notificacion:** Mensaje automatico generado por el sistema para informar al usuario sobre eventos relevantes (solicitudes nuevas, aprobaciones, rechazos, vencimientos).

---

## 6. REQUISITOS TECNOLOGICOS PARA ACCEDER AL APLICATIVO

Es necesario que el usuario cuente con conectividad a internet y acceda al aplicativo a traves de alguno de los siguientes navegadores web compatibles:

| Navegador | Version Minima Recomendada |
|-----------|--------------------------|
| Google Chrome | Version 90 o superior |
| Mozilla Firefox | Version 88 o superior |
| Microsoft Edge | Version 90 o superior |

**Requisitos adicionales:**

| Requisito | Especificacion |
|-----------|---------------|
| Conexion a Internet | Banda ancha (minimo 1 Mbps) |
| Resolucion de pantalla | 1024 x 768 pixeles o superior |
| JavaScript | Debe estar habilitado en el navegador |
| Cookies | Deben estar habilitadas en el navegador |
| Dispositivos compatibles | PC de escritorio, computador portatil, tablet o telefono movil |

**Nota:** El sistema es responsivo y se adapta automaticamente al tamano de la pantalla del dispositivo. Se recomienda el uso de Google Chrome para una experiencia optima.

---

## 7. FUNCIONES DE LOS ROLES DEL SISTEMA

Dentro del Modulo INFRASTOCK se han definido los siguientes roles de usuario, cada uno con funciones especificas:

### 7.1 Administrador

| Funcion | Descripcion |
|---------|------------|
| Gestionar inventario de insumos | Crear, editar, eliminar y consultar insumos del almacen. |
| Gestionar herramientas | Crear, editar, eliminar y consultar herramientas del almacen. |
| Gestionar categorias | Crear, editar y eliminar categorias de insumos y herramientas. |
| Gestionar areas productivas | Crear, editar y eliminar areas productivas del centro. |
| Administrar usuarios | Crear, editar, activar, desactivar y eliminar usuarios del sistema. |
| Aprobar/rechazar solicitudes | Revisar y procesar solicitudes de insumos de los usuarios. |
| Aprobar/rechazar prestamos | Revisar y procesar solicitudes de prestamo de herramientas. |
| Aprobar/rechazar devoluciones | Revisar y procesar devoluciones de herramientas e insumos sobrantes. |
| Consultar reportes | Visualizar graficos y estadisticas del inventario. |
| Recibir notificaciones | Recibir alertas de solicitudes nuevas, vencimientos y sobrantes reportados. |

### 7.2 Operario

| Funcion | Descripcion |
|---------|------------|
| Solicitar insumos | Realizar solicitudes de insumos al administrador. |
| Consultar stock | Verificar la disponibilidad de insumos en el almacen. |
| Reportar sobrantes | Informar sobre insumos no utilizados para devolucion al almacen. |
| Recibir notificaciones | Recibir alertas sobre el estado de sus solicitudes. |

### 7.3 Personal de Aseo

| Funcion | Descripcion |
|---------|------------|
| Solicitar insumos de limpieza | Realizar solicitudes de productos de aseo y limpieza. |
| Reportar sobrantes | Informar sobre productos no utilizados para devolucion al almacen. |
| Recibir notificaciones | Recibir alertas sobre el estado de sus solicitudes. |

### 7.4 Instructor

| Funcion | Descripcion |
|---------|------------|
| Solicitar prestamo de herramientas | Realizar solicitudes de prestamo de herramientas. |
| Registrar devolucion | Registrar la devolucion de herramientas prestadas. |
| Recibir notificaciones | Recibir alertas sobre el estado de sus prestamos. |

### 7.5 Personal de Area (Ganaderia, Agroindustria, Ciencias Basicas, Convivencia, Vigilancia, Psicola)

| Funcion | Descripcion |
|---------|------------|
| Solicitar insumos | Realizar solicitudes de insumos segun las necesidades de su area. |
| Consultar stock | Verificar la disponibilidad de insumos en el almacen. |
| Reportar sobrantes | Informar sobre insumos no utilizados para devolucion al almacen. |
| Recibir notificaciones | Recibir alertas sobre el estado de sus solicitudes. |

---

## 8. FUNCIONALIDADES MODULO ADMINISTRACION DE INVENTARIO

Dentro de las funcionalidades del Modulo INFRASTOCK para el rol Administrador, se cuenta con:

- **Dashboard:** Visualizacion de indicadores clave (solicitudes pendientes, stock disponible, herramientas en prestamo, insumos por vencer) y graficos de consumo.
- **Gestion de Insumos:** Registro, consulta, edicion y eliminacion de insumos, con control de stock, fechas de vencimiento y alertas de minimo.
- **Gestion de Herramientas:** Registro, consulta, edicion y eliminacion de herramientas, con carga de imagen, control de estado y seguimiento de mantenimiento.
- **Gestion de Categorias:** Creacion y administracion de categorias para clasificar insumos y herramientas.
- **Gestion de Areas Productivas:** Creacion y administracion de las areas organizacionales del centro.
- **Administracion de Usuarios:** Registro, edicion, activacion/desactivacion, asignacion de roles y eliminacion de usuarios del sistema.
- **Gestion de Solicitudes:** Revision, aprobacion y rechazo de solicitudes de insumos, con generacion automatica de movimientos de almacen y notificaciones.
- **Gestion de Prestamos y Devoluciones:** Aprobacion y rechazo de solicitudes de prestamo de herramientas y procesamiento de devoluciones.
- **Gestion de Sobrantes:** Revision, aprobacion y rechazo de reportes de insumos sobrantes, con restauracion automatica de stock.
- **Notificaciones:** Sistema de alertas en tiempo real sobre eventos del sistema.

---

## 9. INGRESO AL MODULO INFRASTOCK

El cumplimiento de los siguientes pasos le permitira acceder al Modulo INFRASTOCK como Administrador:

**PASO 1:** Ingrese la URL del sistema

**PASO 2:** Autentiquese con sus credenciales

**PASO 3:** Acceda al Dashboard del Administrador

### 9.1 Pasos

**PASO 1: Ingrese la URL del sistema**

Abra su navegador web (Chrome, Edge o Firefox) e ingrese la direccion URL del sistema INFRASTOCK:

```
https://[dominio]/infrastock
```

Se mostrara la pantalla de bienvenida del modulo INFRASTOCK.

---

**PASO 2: Autentiquese con sus credenciales**

El sistema lo redirigira a la pantalla de autenticacion de SICA. Ingrese los siguientes datos:

| Campo | Descripcion |
|-------|------------|
| **Usuario** | Nombre de usuario asignado por el sistema SICA. |
| **Contrasena** | Contrasena personal e intransferible. |

Una vez ingresados los datos, presione el boton **"Iniciar Sesion"**.

El usuario y la contrasena de acceso al aplicativo INFRASTOCK son personales e intransferibles, y tanto la informacion registrada como los procesos informaticos realizados con las mismas, tienen plena validez.

**Nota:** Si las credenciales ingresadas no son validas, el sistema desplegara un mensaje de error indicando que el usuario o la contrasena son incorrectos. Verifique los datos e intente nuevamente.

---

**PASO 3: Acceda al Dashboard del Administrador**

Una vez autenticado satisfactoriamente, el sistema detectara su rol de **Administrador** y lo redirigira automaticamente al Dashboard principal del modulo INFRASTOCK.

El Dashboard presenta la siguiente informacion:

| Seccion | Descripcion |
|---------|------------|
| **Tarjeta: Solicitudes Nuevas** | Cantidad de solicitudes de insumos pendientes de revision (ultimos 30 dias). |
| **Tarjeta: Insumos en Stock** | Cantidad total o porcentaje de insumos disponibles en el almacen. |
| **Tarjeta: Herramientas en Prestamo** | Cantidad de herramientas actualmente prestadas a usuarios. |
| **Tarjeta: Insumos por Vencer** | Cantidad de insumos con fecha de vencimiento dentro de los proximos 30 dias. |
| **Grafico: Consumo por Area** | Grafico de barras que muestra el consumo de insumos por cada area productiva. |
| **Icono de Notificaciones** | Campana en la parte superior con conteo de notificaciones sin leer. |

---

## 10. OPERACION DEL MODULO ADMINISTRACION DE INVENTARIO

Corresponde la operacion del Modulo INFRASTOCK al usuario con rol **Administrador**, donde podra gestionar insumos, herramientas, categorias, areas productivas, usuarios, solicitudes, prestamos, devoluciones, sobrantes y notificaciones.

---

### 10.1 Gestionar Insumos

**Ruta de acceso:** Menu lateral > **Insumos**

Esta opcion permite al Administrador registrar, consultar, editar y eliminar los insumos del almacen.

#### 10.1.1 Consultar Insumos

**PASO 1:** En el menu lateral izquierdo, seleccione la opcion **"Insumos"**.

**PASO 2:** Se desplegara una tabla con el listado de todos los insumos registrados en el sistema, mostrando: Nombre, Categoria, Cantidad Inicial, Cantidad Disponible, Unidad de Medida, Estado y Fecha de Vencimiento.

**PASO 3:** Utilice la **barra de busqueda** ubicada en la parte superior de la tabla para filtrar insumos por nombre, categoria o caracteristicas.

**PASO 4:** La tabla presenta paginacion de 15 registros por pagina. Utilice los controles de paginacion para navegar entre las paginas.

Los insumos se visualizan con los siguientes estados:

| Estado | Indicador | Descripcion |
|--------|-----------|------------|
| **Disponible** | Verde | El insumo tiene stock suficiente por encima del minimo configurado. |
| **Bajo Stock** | Amarillo | La cantidad disponible esta por debajo del stock minimo configurado. |
| **Critico** | Naranja | La cantidad disponible es muy baja y requiere atencion inmediata. |
| **Agotado** | Rojo | No hay unidades disponibles del insumo en el almacen. |
| **Vencido** | Rojo oscuro | El insumo ha superado su fecha de vencimiento y no debe ser entregado. |

#### 10.1.2 Registrar Nuevo Insumo

**PASO 1:** Haga clic en el boton **"Nuevo Insumo"** (o icono "+") ubicado en la parte superior de la tabla.

**PASO 2:** Se desplegara un formulario modal. Diligencie los datos solicitados:

| Campo | Obligatorio | Descripcion |
|-------|-------------|------------|
| Nombre | Si | Nombre descriptivo del insumo (maximo 255 caracteres). |
| Categoria | Si | Seleccione una categoria de tipo "supply" del listado desplegable. |
| Caracteristicas | No | Descripcion tecnica o especificaciones del insumo. |
| Cantidad inicial | Si | Numero de unidades que ingresan al almacen (minimo 0). |
| Stock minimo | No | Cantidad minima antes de que el sistema genere alerta de bajo stock. |
| Unidad de medida | No | Tipo de medida: Unidad, Kilogramo, Litro, Metro, entre otros (maximo 50 caracteres). |
| Precio | No | Precio unitario del insumo (valor numerico, minimo 0). |
| Fecha de vencimiento | No | Fecha en que el insumo expira. El sistema generara alerta 30 dias antes. |
| Observaciones | No | Notas adicionales sobre el insumo. |
| Inventario | No | Inventario al que pertenece el insumo. |
| Labor | No | Labor asociada al insumo. |

**PASO 3:** Una vez diligenciados los datos, presione el boton **"Guardar"**.

**PASO 4:** El sistema confirmara la creacion exitosa del insumo a traves de un mensaje de operacion exitosa en la parte superior de la pantalla. El nuevo insumo aparecera en el listado.

#### 10.1.3 Editar Insumo

**PASO 1:** En la tabla de insumos, ubique el insumo que desea modificar.

**PASO 2:** Haga clic en el icono de **editar** (icono de lapiz) en la columna de acciones.

**PASO 3:** Se desplegara el formulario modal con los datos actuales del insumo. Modifique los campos necesarios.

**PASO 4:** Presione el boton **"Actualizar"** para guardar los cambios.

**PASO 5:** El sistema confirmara la actualizacion exitosa del insumo.

#### 10.1.4 Eliminar Insumo

**PASO 1:** En la tabla de insumos, ubique el insumo que desea eliminar.

**PASO 2:** Haga clic en el icono de **eliminar** (icono de papelera) en la columna de acciones.

**PASO 3:** Se desplegara un dialogo de confirmacion solicitando verificar la accion.

**PASO 4:** Presione **"Si, eliminar"** para confirmar o **"Cancelar"** para desistir.

**PASO 5:** El sistema confirmara la eliminacion exitosa. El insumo sera eliminado de forma logica (soft delete) y dejara de aparecer en el listado activo.

#### 10.1.5 Ver Detalle de Insumo

**PASO 1:** Haga clic en el nombre del insumo o en el icono de **ver** (icono de ojo).

**PASO 2:** Se mostrara una vista detallada con toda la informacion del insumo, incluyendo el historial de movimientos asociados.

---

### 10.2 Gestionar Herramientas

**Ruta de acceso:** Menu lateral > **Herramientas**

Esta opcion permite al Administrador registrar, consultar, editar y eliminar las herramientas del almacen.

#### 10.2.1 Consultar Herramientas

**PASO 1:** En el menu lateral izquierdo, seleccione la opcion **"Herramientas"**.

**PASO 2:** Se desplegara una tabla con el listado de todas las herramientas registradas, mostrando: Imagen, Nombre, Placa, Marca, Modelo, Categoria, Estado y Cantidad Disponible.

**PASO 3:** Utilice la barra de busqueda para filtrar por nombre, placa, marca u otras caracteristicas.

Las herramientas se visualizan con los siguientes estados:

| Estado | Descripcion |
|--------|------------|
| **Disponible** | La herramienta esta en el almacen y puede ser prestada. |
| **En Prestamo** | La herramienta esta actualmente prestada a un usuario. |
| **Mantenimiento** | La herramienta esta en proceso de reparacion o revision tecnica. |
| **No Disponible** | La herramienta no esta disponible por otra razon. |

#### 10.2.2 Registrar Nueva Herramienta

**PASO 1:** Haga clic en el boton **"Nueva Herramienta"**.

**PASO 2:** Se desplegara un formulario modal. Diligencie los datos solicitados:

| Campo | Obligatorio | Descripcion |
|-------|-------------|------------|
| Nombre | Si | Nombre descriptivo de la herramienta (maximo 255 caracteres). |
| Imagen | No | Fotografia de la herramienta. Formatos aceptados: JPEG, PNG, JPG, GIF. Tamano maximo: 2 MB. |
| Placa | No | Numero de placa o identificacion del activo fijo (maximo 255 caracteres). |
| Descripcion | No | Descripcion detallada de la herramienta y sus especificaciones. |
| Marca | No | Marca del fabricante de la herramienta (maximo 255 caracteres). |
| Modelo | No | Modelo especifico de la herramienta (maximo 255 caracteres). |
| Categoria | No | Seleccione una categoria de tipo "tool" del listado desplegable. |
| Estado | No | Estado actual: disponible, en_prestamo, mantenimiento, no_disponible. |
| Cantidad total | No | Numero total de unidades de esta herramienta (minimo 0). |
| Cantidad disponible | No | Numero de unidades actualmente disponibles para prestamo (minimo 0). |
| Fecha de adquisicion | No | Fecha en que se adquirio la herramienta. |
| Fecha de mantenimiento | No | Ultima fecha en que se realizo mantenimiento. |
| Proximo mantenimiento | No | Fecha programada para el proximo mantenimiento preventivo. |
| Precio | No | Valor del activo (numerico, minimo 0). |

**PASO 3:** Presione el boton **"Guardar"** para registrar la herramienta.

**PASO 4:** El sistema confirmara la creacion exitosa.

#### 10.2.3 Editar Herramienta

**PASO 1:** Haga clic en el icono de **editar** de la herramienta deseada.

**PASO 2:** Modifique los campos necesarios. Si desea cambiar la imagen, seleccione una nueva imagen; la anterior sera reemplazada automaticamente.

**PASO 3:** Presione **"Actualizar"** para guardar los cambios.

#### 10.2.4 Eliminar Herramienta

**PASO 1:** Haga clic en el icono de **eliminar** de la herramienta deseada.

**PASO 2:** Confirme la eliminacion en el dialogo de confirmacion.

---

### 10.3 Gestionar Categorias

**Ruta de acceso:** Menu lateral > **Categorias**

Esta opcion permite al Administrador crear y administrar las categorias para clasificar insumos y herramientas.

#### 10.3.1 Consultar Categorias

**PASO 1:** En el menu lateral, seleccione **"Categorias"**.

**PASO 2:** Se desplegara la tabla con todas las categorias registradas, mostrando: Nombre y Tipo (Insumo o Herramienta).

#### 10.3.2 Registrar Nueva Categoria

**PASO 1:** Haga clic en **"Nueva Categoria"**.

**PASO 2:** Diligencie los datos del formulario modal:

| Campo | Obligatorio | Descripcion |
|-------|-------------|------------|
| Nombre | Si | Nombre de la categoria (maximo 255 caracteres). |
| Tipo | Si | Seleccione: **"supply"** (para categorizar insumos) o **"tool"** (para categorizar herramientas). |

**PASO 3:** Presione **"Guardar"** para crear la categoria.

#### 10.3.3 Editar Categoria

**PASO 1:** Haga clic en el icono de **editar** de la categoria.

**PASO 2:** Modifique el nombre o tipo segun corresponda.

**PASO 3:** Presione **"Actualizar"**.

#### 10.3.4 Eliminar Categoria

**PASO 1:** Haga clic en el icono de **eliminar**.

**PASO 2:** Confirme la eliminacion en el dialogo.

**Nota:** No se recomienda eliminar categorias que tengan insumos o herramientas asociados, ya que podria afectar la clasificacion del inventario.

---

### 10.4 Gestionar Areas Productivas

**Ruta de acceso:** Menu lateral > **Areas Productivas**

Esta opcion permite al Administrador crear y administrar las areas organizacionales del centro de formacion.

#### 10.4.1 Consultar Areas

**PASO 1:** En el menu lateral, seleccione **"Areas Productivas"**.

**PASO 2:** Se desplegara la tabla con todas las areas registradas.

#### 10.4.2 Registrar Nueva Area

**PASO 1:** Haga clic en **"Nueva Area"**.

**PASO 2:** Diligencie los datos del formulario modal:

| Campo | Obligatorio | Descripcion |
|-------|-------------|------------|
| Nombre | Si | Nombre del area productiva (maximo 255 caracteres). Debe ser unico. |
| Descripcion | No | Descripcion del area y sus funciones. |

**PASO 3:** Presione **"Guardar"**.

**Nota:** Si el nombre ingresado ya existe en el sistema, se desplegara un mensaje de error indicando que el nombre esta duplicado.

#### 10.4.3 Editar Area

**PASO 1:** Haga clic en el icono de **editar** del area.

**PASO 2:** Modifique los campos necesarios.

**PASO 3:** Presione **"Actualizar"**.

#### 10.4.4 Eliminar Area

**PASO 1:** Haga clic en el icono de **eliminar**.

**PASO 2:** Confirme la eliminacion.

---

### 10.5 Administrar Usuarios

**Ruta de acceso:** Menu lateral > **Usuarios**

Esta opcion permite al Administrador registrar, consultar, editar, activar, desactivar y eliminar usuarios del sistema INFRASTOCK, asi como asignar los roles correspondientes.

#### 10.5.1 Consultar Usuarios

**PASO 1:** En el menu lateral, seleccione **"Usuarios"**.

**PASO 2:** Se desplegara una tabla con los usuarios registrados en INFRASTOCK, mostrando: Nombre Completo, Tipo y Numero de Documento, Correo Electronico, Rol Asignado y Estado (Activo/Inactivo).

**PASO 3:** Utilice la barra de busqueda para filtrar por nombre, documento o correo electronico.

#### 10.5.2 Registrar Nuevo Usuario

**PASO 1:** Haga clic en el boton **"Nuevo Usuario"**.

**PASO 2:** Se abrira el formulario de creacion de usuario. Diligencie los siguientes datos:

| Campo | Obligatorio | Descripcion |
|-------|-------------|------------|
| Primer nombre | Si | Nombre del usuario (maximo 50 caracteres). |
| Primer apellido | Si | Apellido del usuario (maximo 50 caracteres). |
| Segundo apellido | No | Segundo apellido (maximo 50 caracteres). |
| Tipo de documento | Si | Tipo de identificacion: CC, TI, CE, PP, entre otros. |
| Numero de documento | Si | Numero de documento de identidad (maximo 20 caracteres). Debe ser unico en el sistema. |
| Correo electronico | Si | Direccion de correo electronico (maximo 100 caracteres). Debe ser unico en el sistema. |
| Telefono | No | Numero de telefono de contacto (maximo 20 caracteres). |
| Direccion | No | Direccion de residencia (maximo 200 caracteres). |
| Rol | Si | Seleccione el rol del usuario del listado desplegable. |
| Estado | Si | Seleccione: Activo (1) o Inactivo (0). |
| Contrasena | No | Contrasena de acceso (minimo 8 caracteres). Si no se ingresa, se generara automaticamente. |
| Confirmar contrasena | No | Debe coincidir exactamente con la contrasena ingresada. |

**PASO 3:** Presione el boton **"Crear Usuario"** para finalizar el registro.

**PASO 4:** El sistema confirmara la creacion exitosa del usuario.

**Generacion automatica de contrasena:**

Si el campo de contrasena se deja vacio, el sistema generara automaticamente una contrasena con el siguiente formato:

- Primeras 2 letras del nombre (en minuscula) + primeras 2 letras del primer apellido (en minuscula) + ultimos 4 digitos del numero de documento.
- **Ejemplo:** Para el usuario "Juan Perez" con documento "1234567890", la contrasena generada sera: `jupe7890`.

**Roles disponibles para asignacion:**

| Rol | Descripcion |
|-----|------------|
| Administrador | Acceso total al sistema INFRASTOCK. |
| Operario | Solicitud de insumos, consulta de stock y reporte de sobrantes. |
| Aseo / Personal de Aseo | Solicitud de insumos de limpieza y reporte de sobrantes. |
| Ganaderia | Solicitud de insumos del area de ganaderia. |
| Centro de Convivencia | Solicitud de insumos del centro de convivencia. |
| Vigilancia | Solicitud de insumos del area de vigilancia. |
| Agroindustria | Solicitud de insumos del area de agroindustria. |
| Ciencias Basicas | Solicitud de insumos del area de ciencias basicas. |
| Psicola | Solicitud de insumos del area de psicola. |
| Instructor | Solicitud de prestamos de herramientas para formacion. |

#### 10.5.3 Ver Detalle de Usuario

**PASO 1:** Haga clic en el nombre del usuario o en el icono de **ver** (icono de ojo).

**PASO 2:** Se mostrara la vista detallada con: informacion personal, datos de la cuenta, rol asignado y estadisticas de actividad en el sistema.

#### 10.5.4 Editar Usuario

**PASO 1:** Haga clic en el icono de **editar** del usuario.

**PASO 2:** Modifique los campos necesarios. Para cambiar la contrasena, ingrese la nueva contrasena y su confirmacion.

**PASO 3:** Presione **"Actualizar"** para guardar los cambios.

#### 10.5.5 Activar / Desactivar Usuario

**PASO 1:** En la tabla de usuarios, ubique el usuario que desea activar o desactivar.

**PASO 2:** Haga clic en el boton de **estado** (toggle) del usuario.

**PASO 3:** El sistema cambiara el estado del usuario:

| Accion | Resultado |
|--------|----------|
| **Activo a Inactivo** | El usuario pierde el acceso al sistema y no podra iniciar sesion. |
| **Inactivo a Activo** | El usuario recupera el acceso al sistema con su rol y permisos vigentes. |

**PASO 4:** Se desplegara un mensaje confirmando el cambio de estado.

#### 10.5.6 Eliminar Usuario

**PASO 1:** Haga clic en el icono de **eliminar** del usuario.

**PASO 2:** Confirme la eliminacion en el dialogo de confirmacion.

**Advertencia:** Esta accion elimina permanentemente al usuario, sus roles asignados y su informacion personal del sistema. Esta accion no se puede deshacer.

---

### 10.6 Gestionar Solicitudes de Insumos

**Ruta de acceso:** Menu lateral > **Solicitudes** o **Solicitudes de Insumos**

Esta opcion permite al Administrador revisar, aprobar o rechazar las solicitudes de insumos realizadas por los usuarios de las diferentes areas productivas.

#### 10.6.1 Consultar Solicitudes Pendientes

**PASO 1:** En el menu lateral, seleccione **"Solicitudes"**.

**PASO 2:** Se desplegara una tabla con las solicitudes, mostrando: Solicitante, Area Productiva, Fecha de Solicitud, Cantidad de Items y Estado.

**PASO 3:** En la parte superior se muestran tarjetas resumen con: Total Pendientes, Total Aprobadas y Total Rechazadas.

**Nota:** La lista de solicitudes se actualiza automaticamente cada 30 segundos.

#### 10.6.2 Aprobar Solicitud

**PASO 1:** Ubique la solicitud con estado **"Pendiente"** que desea aprobar.

**PASO 2:** Haga clic en el boton **"Aprobar"** (color verde).

**PASO 3:** Se desplegara un dialogo de confirmacion mostrando el detalle de los items solicitados.

**PASO 4:** Presione **"Si, aprobar"** para confirmar la aprobacion.

**PASO 5:** El sistema realizara automaticamente las siguientes acciones:

| Accion | Descripcion |
|--------|------------|
| Actualizar estado de la solicitud | Cambia a **"Aprobada"** con fecha y hora de aprobacion. |
| Actualizar estado de los items | Todos los items de la solicitud cambian a **"Aprobado"**. |
| Verificar stock disponible | Para cada insumo solicitado, verifica que haya stock suficiente. |
| Crear movimiento de almacen | Registra un movimiento de tipo **"Entrega"** por cada item aprobado. |
| Crear registro de sobrante | Crea un registro de sobrante en estado **"Pendiente"** para seguimiento de devolucion. |
| Enviar notificacion | Envia una notificacion de tipo **"request_approved"** al usuario solicitante. |

**PASO 6:** Se desplegara un mensaje de operacion exitosa.

#### 10.6.3 Rechazar Solicitud

**PASO 1:** Ubique la solicitud con estado **"Pendiente"** que desea rechazar.

**PASO 2:** Haga clic en el boton **"Rechazar"** (color rojo).

**PASO 3:** Se desplegara un dialogo solicitando el **motivo de rechazo**. Este campo es obligatorio (maximo 500 caracteres).

**PASO 4:** Ingrese el motivo de rechazo y presione **"Rechazar"**.

**PASO 5:** El sistema realizara automaticamente las siguientes acciones:

| Accion | Descripcion |
|--------|------------|
| Actualizar estado de la solicitud | Cambia a **"Rechazada"** con fecha y motivo de rechazo. |
| Actualizar estado de los items | Todos los items de la solicitud cambian a **"Rechazado"**. |
| Enviar notificacion | Envia una notificacion de tipo **"request_rejected"** al usuario solicitante con el motivo de rechazo. |

**PASO 6:** Se desplegara un mensaje de operacion exitosa.

---

### 10.7 Gestionar Prestamos y Devoluciones de Herramientas

**Ruta de acceso:** Menu lateral > **Prestamos** o **Prestamos y Devoluciones**

Esta opcion permite al Administrador gestionar los prestamos y devoluciones de herramientas solicitados por los usuarios del sistema.

#### 10.7.1 Consultar Prestamos y Devoluciones

**PASO 1:** En el menu lateral, seleccione **"Prestamos"**.

**PASO 2:** Se desplegara una tabla con todos los movimientos de prestamo y devolucion de herramientas, mostrando: Herramienta, Usuario, Tipo (Prestamo/Devolucion), Fecha y Estado.

**PASO 3:** Se muestra un contador de devoluciones pendientes de procesamiento.

#### 10.7.2 Aprobar Prestamo de Herramienta

**PASO 1:** Ubique el prestamo con estado **"Pendiente"** y tipo **"Prestamo"**.

**PASO 2:** Haga clic en el boton **"Aprobar"**.

**PASO 3:** Confirme la aprobacion en el dialogo.

**PASO 4:** El sistema actualizara el estado del prestamo a **"Aprobado"** y enviara una notificacion de tipo **"loan_approved"** al usuario solicitante.

#### 10.7.3 Rechazar Prestamo de Herramienta

**PASO 1:** Ubique el prestamo pendiente que desea rechazar.

**PASO 2:** Haga clic en el boton **"Rechazar"**.

**PASO 3:** Ingrese el **motivo de rechazo** (obligatorio, maximo 500 caracteres).

**PASO 4:** Presione **"Rechazar"** para confirmar.

**PASO 5:** El sistema actualizara el estado a **"Rechazado"**, registrara el motivo y enviara una notificacion de tipo **"loan_rejected"** al usuario solicitante.

#### 10.7.4 Aprobar Devolucion de Herramienta

**PASO 1:** Ubique la devolucion con estado **"Pendiente"** y tipo **"Devolucion"**.

**PASO 2:** Haga clic en el boton **"Aprobar Devolucion"**.

**PASO 3:** Confirme la aprobacion.

**PASO 4:** El sistema realizara las siguientes acciones:

| Accion | Descripcion |
|--------|------------|
| Actualizar estado | La devolucion cambia a **"Aprobada"**. |
| Crear movimiento de almacen | Registra un movimiento de tipo **"Recibe"** para restaurar el stock de la herramienta en el inventario. |
| Enviar notificacion | Envia una notificacion de tipo **"return_approved"** al usuario. |

#### 10.7.5 Rechazar Devolucion de Herramienta

**PASO 1:** Ubique la devolucion pendiente.

**PASO 2:** Haga clic en **"Rechazar Devolucion"**.

**PASO 3:** Ingrese el motivo de rechazo (obligatorio, maximo 500 caracteres).

**PASO 4:** El sistema actualizara el estado a **"Rechazada"**, registrara el motivo y enviara una notificacion de tipo **"return_rejected"** al usuario.

---

### 10.8 Gestionar Devoluciones de Insumos (Sobrantes)

**Ruta de acceso:** Menu lateral > **Devoluciones de Insumos** o **Sobrantes**

Esta opcion permite al Administrador gestionar los reportes de sobrantes (insumos no utilizados) enviados por el personal de las areas productivas.

#### 10.8.1 Consultar Devoluciones de Insumos

**PASO 1:** En el menu lateral, seleccione **"Devoluciones de Insumos"**.

**PASO 2:** Se desplegara una tabla con todos los reportes de sobrantes. En la parte superior se muestran tarjetas resumen:

| Tarjeta | Descripcion |
|---------|------------|
| **Pendientes** | Cantidad de reportes de sobrantes que esperan revision. |
| **Aprobados** | Cantidad de sobrantes aprobados y devueltos al inventario. |
| **Rechazados** | Cantidad de sobrantes rechazados. |
| **Total** | Cantidad total de reportes de sobrantes. |

**PASO 3:** Utilice el **filtro por estado** para mostrar solo: Todos, Pendientes, Aprobados o Rechazados.

**PASO 4:** La tabla presenta paginacion de 50 registros por pagina.

#### 10.8.2 Aprobar Sobrante

**PASO 1:** Ubique el reporte de sobrante con estado **"Pendiente"**.

**PASO 2:** Haga clic en el boton **"Aprobar"**.

**PASO 3:** Confirme la aprobacion en el dialogo.

**PASO 4:** El sistema realizara las siguientes acciones:

| Accion | Descripcion |
|--------|------------|
| Crear movimiento de almacen | Registra un movimiento de tipo **"Recibe"** para devolver las unidades sobrantes al stock del almacen. |
| Actualizar estado del sobrante | Cambia a **"Aprobado"**. |
| Registrar procesamiento | Registra la fecha de procesamiento y el nombre del administrador que proceso el sobrante. |

#### 10.8.3 Rechazar Sobrante

**PASO 1:** Ubique el reporte de sobrante pendiente.

**PASO 2:** Haga clic en **"Rechazar"**.

**PASO 3:** Ingrese el **motivo de rechazo** (obligatorio, maximo 500 caracteres).

**PASO 4:** El sistema actualizara el estado a **"Rechazado"** y registrara el motivo, la fecha y el administrador que proceso el rechazo.

---

### 10.9 Gestionar Notificaciones

**Ruta de acceso:** Icono de campana en la barra superior

El sistema de notificaciones mantiene al Administrador informado en tiempo real sobre los eventos relevantes del modulo.

#### 10.9.1 Consultar Notificaciones

**PASO 1:** Haga clic en el icono de **campana** ubicado en la barra superior de navegacion.

**PASO 2:** Se desplegara la lista de notificaciones recientes (ultimos 30 dias).

**PASO 3:** El numero que aparece junto a la campana indica las notificaciones **sin leer**.

#### 10.9.2 Tipos de Notificaciones del Administrador

| Tipo | Descripcion |
|------|------------|
| **request_created** | Un usuario de area productiva ha creado una nueva solicitud de insumos. |
| **surplus_reported** | El personal de aseo u otro rol ha reportado un sobrante de insumos. |
| **loan_created** | Un instructor u otro usuario ha solicitado el prestamo de una herramienta. |
| **supply_expiring** | Un insumo del almacen esta proximo a su fecha de vencimiento (30 dias o menos). |

#### 10.9.3 Marcar Notificacion como Leida

**PASO 1:** Haga clic en la notificacion que desea marcar como leida.

**PASO 2:** La notificacion se marcara automaticamente como leida y el contador de notificaciones se actualizara.

---

### 10.10 Editar Perfil del Administrador

**Ruta de acceso:** Menu de usuario (esquina superior derecha) > **Mi Perfil**

**PASO 1:** Haga clic en su nombre de usuario en la esquina superior derecha de la pantalla.

**PASO 2:** Seleccione la opcion **"Mi Perfil"** o **"Editar Perfil"**.

**PASO 3:** Modifique los datos que desee actualizar:

| Campo | Descripcion |
|-------|------------|
| Nombre | Nombre del administrador. |
| Apellido | Apellido del administrador. |
| Correo electronico | Direccion de correo electronico. |
| Contrasena | Nueva contrasena (opcional, requiere confirmacion). |

**PASO 4:** Presione **"Guardar Cambios"** para actualizar su perfil.

---

### 10.11 Cerrar Sesion

**PASO 1:** Haga clic en su nombre de usuario en la esquina superior derecha.

**PASO 2:** Seleccione la opcion **"Cerrar Sesion"**.

**PASO 3:** El sistema cerrara su sesion de forma segura y lo redirigira a la pantalla de inicio de sesion.

**Nota:** Se recomienda siempre cerrar sesion al finalizar el uso del sistema, especialmente cuando se accede desde equipos compartidos o publicos.

---

## 11. MENSAJES DE ADVERTENCIA DEL MODULO

A continuacion se relacionan los principales mensajes de advertencia y error que puede generar el sistema durante la operacion del Modulo INFRASTOCK:

| Mensaje | Causa | Solucion |
|---------|-------|----------|
| "Usuario o contrasena incorrectos" | Las credenciales ingresadas no son validas. | Verifique el nombre de usuario y la contrasena. Si persiste, contacte al administrador de SICA. |
| "El campo [nombre] es obligatorio" | Se intento guardar un registro sin completar un campo requerido. | Complete todos los campos marcados como obligatorios en el formulario. |
| "El numero de documento ya esta registrado" | Se intento crear un usuario con un documento que ya existe en el sistema. | Verifique si el usuario ya esta registrado o utilice un numero de documento diferente. |
| "El correo electronico ya esta registrado" | Se intento crear un usuario con un email que ya existe. | Utilice una direccion de correo electronico diferente. |
| "El nombre del area ya existe" | Se intento crear un area productiva con un nombre duplicado. | Utilice un nombre diferente para el area. |
| "No hay stock suficiente" | Se intento aprobar una solicitud pero el insumo no tiene unidades disponibles. | Verifique el stock del insumo antes de aprobar. Considere rechazar o reabastecer el inventario. |
| "La solicitud no puede ser procesada" | Se intento aprobar/rechazar una solicitud que ya fue procesada. | La solicitud ya fue aprobada o rechazada previamente. No se requiere accion adicional. |
| "El motivo de rechazo es obligatorio" | Se intento rechazar una solicitud sin ingresar el motivo. | Ingrese el motivo de rechazo (maximo 500 caracteres) antes de confirmar. |
| "El archivo excede el tamano maximo permitido" | Se intento cargar una imagen superior a 2 MB. | Reduzca el tamano de la imagen y vuelva a intentar. Formatos aceptados: JPEG, PNG, JPG, GIF. |
| "La contrasena debe tener minimo 8 caracteres" | La contrasena ingresada es muy corta. | Ingrese una contrasena de al menos 8 caracteres. |
| "La confirmacion de contrasena no coincide" | Los campos de contrasena y confirmacion no son iguales. | Verifique que ambos campos contengan exactamente la misma contrasena. |
| "Sesion expirada" | La sesion del usuario ha expirado por inactividad. | Vuelva a iniciar sesion con sus credenciales. |
| "Error de conexion" | No se pudo establecer conexion con el servidor. | Verifique su conexion a internet e intente nuevamente. Si persiste, contacte soporte tecnico. |

---

*Fin del Manual de Usuario Administrador - Modulo INFRASTOCK V1.0*

*Centro de Formacion Agropecuaria - SICEFA*

*Febrero 2026*
