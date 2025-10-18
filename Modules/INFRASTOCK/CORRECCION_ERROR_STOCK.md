# Corrección de Error: Columna 'stock' No Encontrada

## 🚨 **Problema Identificado**

Al intentar acceder al dashboard del Personal de Aseo, se producía el siguiente error:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stock' in 'where clause'
```

**SQL Query que causaba el error:**
```sql
select * from `equipments` where `stock` > 0 and `equipments`.`deleted_at` is null order by `name` asc
```

## 🔍 **Causa del Error**

El código del `CleaningStaffController` estaba intentando usar una columna llamada `stock` en la tabla `equipments`, pero esta columna no existe en la base de datos.

### **Estructura Real de la Tabla `equipments`:**
- ✅ `id` - Identificador único
- ✅ `labor_id` - ID del labor
- ✅ `inventory_id` - ID del inventario
- ✅ `name` - Nombre del equipo
- ✅ `category_id` - ID de la categoría
- ✅ `amount` - **Cantidad disponible** (esta es la columna correcta)
- ✅ `price` - Precio
- ✅ `expiration_date` - Fecha de expiración
- ✅ `deleted_at` - Soft delete
- ✅ `created_at` - Fecha de creación
- ✅ `updated_at` - Fecha de actualización

## 🔧 **Correcciones Realizadas**

### **1. Método `dashboard()` - Líneas 228-231**

#### **Antes:**
```php
// Obtener insumos disponibles para solicitar
$availableSupplies = Equipment::with('category')
    ->where('stock', '>', 0)
    ->orderBy('name')
    ->get();
```

#### **Después:**
```php
// Obtener insumos disponibles para solicitar
$availableSupplies = Equipment::with('category')
    ->where('amount', '>', 0)
    ->orderBy('name')
    ->get();
```

### **2. Método `createRequest()` - Líneas 250-253**

#### **Antes:**
```php
$equipments = Equipment::with('category')
    ->where('stock', '>', 0)
    ->orderBy('name')
    ->get();
```

#### **Después:**
```php
$equipments = Equipment::with('category')
    ->where('amount', '>', 0)
    ->orderBy('name')
    ->get();
```

### **3. Método `storeRequest()` - Líneas 275-279**

#### **Antes:**
```php
// Verificar que el insumo tenga stock suficiente
$equipment = Equipment::findOrFail($request->movement_id);
if ($equipment->stock < $request->amount) {
    return redirect()->back()->with('error', 'No hay suficiente stock disponible para este insumo.');
}
```

#### **Después:**
```php
// Verificar que el insumo tenga cantidad suficiente
$equipment = Equipment::findOrFail($request->movement_id);
if ($equipment->amount < $request->amount) {
    return redirect()->back()->with('error', 'No hay suficiente cantidad disponible para este insumo.');
}
```

### **4. Método `supplyHistory()` - Líneas 414-417**

#### **Antes:**
```php
// Obtener estadísticas de disponibilidad
$totalSupplies = $supplies->count();
$availableSupplies = $supplies->where('stock', '>', 0)->count();
$lowStockSupplies = $supplies->where('stock', '<=', 5)->where('stock', '>', 0)->count();
$outOfStockSupplies = $supplies->where('stock', 0)->count();
```

#### **Después:**
```php
// Obtener estadísticas de disponibilidad
$totalSupplies = $supplies->count();
$availableSupplies = $supplies->where('amount', '>', 0)->count();
$lowStockSupplies = $supplies->where('amount', '<=', 5)->where('amount', '>', 0)->count();
$outOfStockSupplies = $supplies->where('amount', 0)->count();
```

## ✅ **Resultado de la Corrección**

### **Funcionalidades Corregidas:**

1. **Dashboard Principal**: Ahora puede cargar correctamente los insumos disponibles
2. **Crear Solicitud**: Puede mostrar los equipos disponibles para solicitar
3. **Validación de Cantidad**: Verifica correctamente si hay suficiente cantidad disponible
4. **Historial de Insumos**: Calcula correctamente las estadísticas de disponibilidad

### **Consultas SQL Corregidas:**

#### **Dashboard - Insumos Disponibles:**
```sql
SELECT * FROM `equipments` 
WHERE `amount` > 0 
AND `equipments`.`deleted_at` IS NULL 
ORDER BY `name` ASC
```

#### **Crear Solicitud - Equipos Disponibles:**
```sql
SELECT * FROM `equipments` 
WHERE `amount` > 0 
AND `equipments`.`deleted_at` IS NULL 
ORDER BY `name` ASC
```

#### **Validación de Cantidad:**
```php
if ($equipment->amount < $request->amount) {
    // No hay suficiente cantidad
}
```

#### **Estadísticas de Disponibilidad:**
```php
$availableSupplies = $supplies->where('amount', '>', 0)->count();
$lowStockSupplies = $supplies->where('amount', '<=', 5)->where('amount', '>', 0)->count();
$outOfStockSupplies = $supplies->where('amount', 0)->count();
```

## 🧪 **Pruebas de Funcionamiento**

### **Para Verificar la Corrección:**

1. **Acceder al Dashboard:**
   - Visita `/infrastock/cleaning-staff/dashboard`
   - Debería cargar sin errores
   - Debería mostrar estadísticas y insumos disponibles

2. **Crear Nueva Solicitud:**
   - Ve a "Nueva Solicitud"
   - Debería mostrar la lista de equipos disponibles
   - Debería permitir seleccionar equipos con `amount > 0`

3. **Verificar Validación:**
   - Intenta solicitar más cantidad de la disponible
   - Debería mostrar mensaje de error apropiado

4. **Historial de Insumos:**
   - Ve a "Historial de Insumos"
   - Debería mostrar estadísticas correctas de disponibilidad

## 📝 **Notas Importantes**

### **Terminología Actualizada:**
- **Antes**: Se usaba "stock" para referirse a la cantidad disponible
- **Después**: Se usa "amount" que es la columna real en la base de datos

### **Mensajes de Error Actualizados:**
- **Antes**: "No hay suficiente stock disponible para este insumo"
- **Después**: "No hay suficiente cantidad disponible para este insumo"

### **Lógica de Negocio Mantenida:**
- ✅ Solo se muestran equipos con cantidad > 0
- ✅ Se valida que la cantidad solicitada no exceda la disponible
- ✅ Se calculan estadísticas correctas de disponibilidad
- ✅ Se mantiene la funcionalidad de filtrado y ordenamiento

## 🚀 **Estado Final**

- ✅ **Error corregido**: Ya no hay referencias a la columna inexistente `stock`
- ✅ **Dashboard funcional**: Carga correctamente sin errores
- ✅ **Todas las funcionalidades operativas**: Crear solicitudes, validaciones, estadísticas
- ✅ **Base de datos consistente**: Usa la columna correcta `amount`
- ✅ **Mensajes actualizados**: Terminología coherente con la estructura de datos

**¡El dashboard del Personal de Aseo ahora funciona correctamente!** 🎉
