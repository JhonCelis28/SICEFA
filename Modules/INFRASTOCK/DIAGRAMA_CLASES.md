# Diagrama de Clases - Módulo INFRASTOCK

## Diagrama UML en PlantUML

```plantuml
@startuml INFRASTOCK_Class_Diagram

' ============================================
' ENTIDADES PRINCIPALES
' ============================================

class Equipment {
    -id: int
    -labor_id: int
    -inventory_id: int
    -name: string
    -characteristics: string
    -amount: int
    -initial_amount: int
    -minimum_stock: int
    -unit_measure: string
    -price: decimal
    -category_id: int
    -expiration_date: date
    -observations: string
    -status: string
    -deleted_at: timestamp
    --
    +getUsedAmountAttribute(): int
    +getInitialAmountAttribute(): int
    +getStockAttribute(): int
    +hasStockFor(amount: int): bool
    +calculateStatus(): string
    +getStatusColorAttribute(): string
    +getStatusTextAttribute(): string
}

class Tool {
    -id: int
    -nombre: string
    -imagen: string
    -placa: string
    -descripcion: string
    -descripcion_actual: string
    -marca: string
    -modelo: string
    -category_id: int
    -estado: string
    -cantidad_total: int
    -cantidad_disponible: int
    -fecha_mantenimiento: date
    -proximo_mantenimiento: date
    -fecha_adquisicion: date
    -inventory_id: int
    -labor_id: int
    -amount: int
    -price: decimal
    -deleted_at: timestamp
}

class InfrastockCategory {
    -id: int
    -name: string
    -type: string
    -deleted_at: timestamp
}

class Request {
    -id: int
    -user_id: int
    -productive_unit_warehouse_id: int
    -description: string
    -status: string
    -approved_at: datetime
    -rejected_at: datetime
    -approved_by: int
    -rejection_reason: string
    -created_at: timestamp
    -updated_at: timestamp
    --
    +getTotalItemsAttribute(): int
    +getTotalRequestedAmountAttribute(): int
    +scopeByUser(query, userId)
    +scopeByStatus(query, status)
    +scopePending(query)
    +scopeApproved(query)
    +scopeRejected(query)
}

class RequestItem {
    -id: int
    -request_id: int
    -equipment_id: int
    -requested_amount: int
    -approved_amount: int
    -delivered_amount: int
    -status: string
    -notes: string
    -created_at: timestamp
    -updated_at: timestamp
    --
    +scopeByStatus(query, status)
    +scopePending(query)
    +scopeApproved(query)
    +scopeRejected(query)
    +scopeDelivered(query)
}

class Surplus {
    -id: int
    -equipment_id: int
    -user_id: int
    -request_id: int
    -request_item_id: int
    -surplus_amount: int
    -reason: string
    -description: string
    -surplus_date: date
    -status: string
    -processed_at: datetime
    -processed_by: int
    -created_at: timestamp
    -updated_at: timestamp
    --
    +scopeByDateRange(query, startDate, endDate)
    +scopeByUser(query, userId)
    +scopeByEquipment(query, equipmentId)
    +scopeByStatus(query, status)
    +isPending(): bool
    +isApproved(): bool
    +isRejected(): bool
}

class WarehouseMovement {
    -id: int
    -productive_unit_warehouse_id: int
    -movement_id: int
    -equipment_id: int
    -role: string
    -user_id: int
    -item_type: string
    -amount: int
    -status: string
    -surplus_id: int
    -description: string
    -imagen: string
    -purpose: string
    -required_date: date
    -delivery_image: string
    -return_image: string
    -deleted_at: timestamp
}

class ProductiveUnit {
    -id: int
    -name: string
    -description: string
    -icon: string
    -person_id: int
    -sector_id: int
    -farm_id: int
    -deleted_at: timestamp
}

class Warehouse {
    -id: int
    -name: string
    -description: string
    -app_id: int
    -deleted_at: timestamp
}

class ProductiveUnitWarehouse {
    -id: int
    -productive_unit_id: int
    -warehouse_id: int
    -deleted_at: timestamp
}

class Inventory {
    -id: int
    -name: string
    -description: string
    -deleted_at: timestamp
}

class Labor {
    -id: int
    -name: string
    -description: string
    -deleted_at: timestamp
}

class Notification {
    -id: int
    -type: string
    -notifiable_type: string
    -notifiable_id: int
    -data: array
    -read_at: datetime
    -created_at: timestamp
    -updated_at: timestamp
    --
    +markAsRead(): void
    +isRead(): bool
    +createRequestCreatedNotification(userId, equipmentName, amount, requestId): Notification
    +createRequestApprovedNotification(userId, equipmentName, amount, requestId): Notification
    +createRequestRejectedNotification(userId, equipmentName, amount, requestId): Notification
}

class User {
    -id: int
    -name: string
    -email: string
    -password: string
    -created_at: timestamp
    -updated_at: timestamp
}

' ============================================
' RELACIONES
' ============================================

' Equipment
Equipment "belongs to" --> InfrastockCategory : category_id
Equipment "belongs to" --> Labor : labor_id
Equipment "belongs to" --> Inventory : inventory_id
Equipment "has many" --> RequestItem : equipment_id
Equipment "has many" --> WarehouseMovement : equipment_id
Equipment "has many" --> Surplus : equipment_id

' Tool
Tool "belongs to" --> InfrastockCategory : category_id
Tool "belongs to" --> Labor : labor_id
Tool "belongs to" --> Inventory : inventory_id
Tool "has many" --> WarehouseMovement : movement_id

' InfrastockCategory
InfrastockCategory "has many" --> Equipment : category_id
InfrastockCategory "has many" --> Tool : category_id

' Request
Request "belongs to" --> User : user_id
Request "belongs to" --> User : approved_by
Request "belongs to" --> ProductiveUnitWarehouse : productive_unit_warehouse_id
Request "has many" --> RequestItem : request_id
Request "has many" --> Surplus : request_id

' RequestItem
RequestItem "belongs to" --> Request : request_id
RequestItem "belongs to" --> Equipment : equipment_id
RequestItem "has many" --> Surplus : request_item_id

' Surplus
Surplus "belongs to" --> Equipment : equipment_id
Surplus "belongs to" --> User : user_id
Surplus "belongs to" --> Request : request_id
Surplus "belongs to" --> RequestItem : request_item_id

' WarehouseMovement
WarehouseMovement "belongs to" --> ProductiveUnitWarehouse : productive_unit_warehouse_id
WarehouseMovement "belongs to" --> User : user_id
WarehouseMovement "belongs to" --> Equipment : equipment_id
WarehouseMovement "belongs to" --> Tool : movement_id
WarehouseMovement "belongs to" --> Surplus : surplus_id

' ProductiveUnitWarehouse
ProductiveUnitWarehouse "belongs to" --> ProductiveUnit : productive_unit_id
ProductiveUnitWarehouse "belongs to" --> Warehouse : warehouse_id
ProductiveUnitWarehouse "has many" --> Request : productive_unit_warehouse_id
ProductiveUnitWarehouse "has many" --> WarehouseMovement : productive_unit_warehouse_id

' Notification
Notification "belongs to" --> User : notifiable_id (polymorphic)

' ============================================
' NOTAS Y OBSERVACIONES
' ============================================

note right of Equipment
  **Cálculo de Stock:**
  stock = initial_amount - used_amount
  used_amount = entregas - recibes
end note

note right of Request
  **Estados:**
  - pending
  - approved
  - rejected
  - delivered
end note

note right of Surplus
  **Estados:**
  - pending
  - approved
  - rejected
end note

note right of WarehouseMovement
  **Tipos de Movimiento:**
  - item_type: 'equipment' | 'tool'
  - role: 'Préstamo' | 'Devolución' | 'Entrega' | 'Recibe'
end note

@enduml
```

## Descripción de las Clases

### 1. **Equipment (Insumo/Equipo)**
Representa los insumos o equipos del inventario. Calcula automáticamente el stock disponible basándose en movimientos de almacén.

**Atributos clave:**
- `amount`: Cantidad actual
- `initial_amount`: Cantidad inicial
- `stock`: Stock disponible (calculado)
- `status`: Estado calculado automáticamente (disponible, agotado, crítico, bajo_stock, vencido)

### 2. **Tool (Herramienta)**
Representa las herramientas del inventario. Similar a Equipment pero para herramientas.

### 3. **InfrastockCategory (Categoría)**
Clasifica tanto insumos como herramientas por tipo.

### 4. **Request (Solicitud)**
Representa una solicitud de insumos realizada por un usuario.

**Estados:** pending, approved, rejected, delivered

### 5. **RequestItem (Item de Solicitud)**
Cada insumo solicitado dentro de una Request. Permite aprobar/rechazar cantidades específicas.

### 6. **Surplus (Sobrante)**
Registra los sobrantes de insumos entregados que no fueron utilizados.

### 7. **WarehouseMovement (Movimiento de Almacén)**
Registra todos los movimientos de inventario (préstamos, devoluciones, entregas).

**Tipos:**
- `item_type`: 'equipment' o 'tool'
- `role`: 'Préstamo', 'Devolución', 'Entrega', 'Recibe'

### 8. **ProductiveUnit (Unidad Productiva)**
Representa las áreas productivas del centro (Agroindustria, Ganadería, etc.).

### 9. **Warehouse (Almacén)**
Representa los almacenes o bodegas donde se guardan los insumos.

### 10. **ProductiveUnitWarehouse (Relación Unidad-Almacén)**
Tabla intermedia que relaciona unidades productivas con almacenes.

### 11. **Notification (Notificación)**
Sistema de notificaciones polimórfico para usuarios.

### 12. **User (Usuario)**
Modelo de usuario del sistema principal (App\Models\User).

## Relaciones Principales

1. **Equipment ↔ InfrastockCategory**: Muchos a Uno
2. **Request ↔ RequestItem**: Uno a Muchos
3. **Request ↔ User**: Muchos a Uno (solicitante y aprobador)
4. **RequestItem ↔ Equipment**: Muchos a Uno
5. **Surplus ↔ Request**: Muchos a Uno
6. **WarehouseMovement ↔ Equipment/Tool**: Muchos a Uno (polimórfico)
7. **ProductiveUnitWarehouse**: Relación intermedia entre ProductiveUnit y Warehouse

## Patrones de Diseño Utilizados

1. **Soft Deletes**: La mayoría de entidades usan borrado lógico
2. **Accessors/Mutators**: Equipment calcula stock y status automáticamente
3. **Scopes**: Request y RequestItem tienen scopes para filtrar por estado
4. **Polymorphic Relations**: Notification usa relaciones polimórficas
5. **Factory Pattern**: Algunas entidades tienen factories para testing

## Herramientas para Visualizar

Puedes visualizar este diagrama usando:

1. **PlantUML Online**: http://www.plantuml.com/plantuml/
2. **VS Code**: Extensión "PlantUML"
3. **IntelliJ IDEA**: Plugin PlantUML
4. **Draw.io**: Importar código PlantUML
5. **Visual Studio**: Extensión PlantUML

## Versión en Mermaid (Alternativa)

```mermaid
classDiagram
    class Equipment {
        +int id
        +int labor_id
        +int inventory_id
        +string name
        +int amount
        +int initial_amount
        +int minimum_stock
        +string unit_measure
        +decimal price
        +int category_id
        +date expiration_date
        +string status
        +getStockAttribute() int
        +hasStockFor(int) bool
        +calculateStatus() string
    }
    
    class Tool {
        +int id
        +string nombre
        +string placa
        +string descripcion
        +string marca
        +string modelo
        +int category_id
        +int cantidad_total
        +int cantidad_disponible
        +date fecha_mantenimiento
        +int amount
        +decimal price
    }
    
    class InfrastockCategory {
        +int id
        +string name
        +string type
    }
    
    class Request {
        +int id
        +int user_id
        +int productive_unit_warehouse_id
        +string description
        +string status
        +datetime approved_at
        +int approved_by
        +getTotalItemsAttribute() int
        +scopePending() query
    }
    
    class RequestItem {
        +int id
        +int request_id
        +int equipment_id
        +int requested_amount
        +int approved_amount
        +int delivered_amount
        +string status
    }
    
    class Surplus {
        +int id
        +int equipment_id
        +int user_id
        +int request_id
        +int surplus_amount
        +string reason
        +date surplus_date
        +string status
        +isPending() bool
        +isApproved() bool
    }
    
    class WarehouseMovement {
        +int id
        +int productive_unit_warehouse_id
        +int equipment_id
        +string role
        +int user_id
        +string item_type
        +int amount
        +string status
    }
    
    class ProductiveUnit {
        +int id
        +string name
        +string description
        +string icon
    }
    
    class Warehouse {
        +int id
        +string name
        +string description
        +int app_id
    }
    
    class ProductiveUnitWarehouse {
        +int id
        +int productive_unit_id
        +int warehouse_id
    }
    
    class Notification {
        +int id
        +string type
        +string notifiable_type
        +int notifiable_id
        +array data
        +datetime read_at
        +markAsRead() void
        +isRead() bool
    }
    
    class User {
        +int id
        +string name
        +string email
    }
    
    Equipment "many" --> "one" InfrastockCategory : category
    Tool "many" --> "one" InfrastockCategory : category
    Equipment "many" --> "one" Labor : labor
    Tool "many" --> "one" Labor : labor
    Equipment "many" --> "one" Inventory : inventory
    Tool "many" --> "one" Inventory : inventory
    
    Request "one" --> "many" RequestItem : items
    RequestItem "many" --> "one" Equipment : equipment
    Request "many" --> "one" User : user
    Request "many" --> "one" User : approver
    Request "many" --> "one" ProductiveUnitWarehouse : productiveUnitWarehouse
    
    Surplus "many" --> "one" Equipment : equipment
    Surplus "many" --> "one" User : user
    Surplus "many" --> "one" Request : request
    Surplus "many" --> "one" RequestItem : requestItem
    
    WarehouseMovement "many" --> "one" ProductiveUnitWarehouse : productiveUnitWarehouse
    WarehouseMovement "many" --> "one" User : user
    WarehouseMovement "many" --> "one" Equipment : equipment
    WarehouseMovement "many" --> "one" Tool : tool
    
    ProductiveUnitWarehouse "many" --> "one" ProductiveUnit : productiveUnit
    ProductiveUnitWarehouse "many" --> "one" Warehouse : warehouse
    
    Notification "many" --> "one" User : notifiable
```

## Diagrama Simplificado de Relaciones

```
┌─────────────────┐
│   User          │
└────────┬────────┘
         │
         │ 1
         │
    ┌────▼─────────────────────────────────────┐
    │         Request                          │
    │  - status: pending/approved/rejected    │
    └────┬─────────────────────────────────────┘
         │ 1
         │
         │ *
    ┌────▼──────────────┐
    │   RequestItem     │
    │  - requested_amount│
    │  - approved_amount │
    └────┬──────────────┘
         │ *
         │
         │ 1
    ┌────▼──────────────┐
    │   Equipment       │
    │  - stock (calc)   │
    │  - status (calc)  │
    └────┬──────────────┘
         │ *
         │
         │ 1
    ┌────▼──────────────┐
    │ InfrastockCategory│
    └───────────────────┘

┌──────────────────────┐
│  ProductiveUnit      │
└──────────┬───────────┘
           │ 1
           │
           │ *
┌──────────▼──────────────────┐
│ ProductiveUnitWarehouse     │
└──────────┬──────────────────┘
           │ 1
           │
           │ 1
┌──────────▼──────────┐
│   Warehouse         │
└─────────────────────┘
```

## Exportar a Otros Formatos

### PlantUML
Para exportar a PNG, SVG o PDF, puedes usar:

```bash
# Con PlantUML instalado
java -jar plantuml.jar DIAGRAMA_CLASES.md

# O usar el servidor online
# Copia el código entre @startuml y @enduml a:
# http://www.plantuml.com/plantuml/uml/
```

### Mermaid
Para visualizar el diagrama Mermaid:
- **GitHub/GitLab**: Se renderiza automáticamente en archivos .md
- **Online**: https://mermaid.live/
- **VS Code**: Extensión "Markdown Preview Mermaid Support"

### Herramientas Recomendadas

1. **PlantUML Online**: http://www.plantuml.com/plantuml/
2. **Mermaid Live Editor**: https://mermaid.live/
3. **Draw.io**: https://app.diagrams.net/ (importar PlantUML)
4. **Lucidchart**: https://www.lucidchart.com/
5. **Visual Paradigm**: https://www.visual-paradigm.com/
