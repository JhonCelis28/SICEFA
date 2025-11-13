<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Insumos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #10B981;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #10B981;
            border-radius: 4px;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #374151;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #10B981;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .equipment-list {
            list-style: none;
            padding: 0;
        }
        .equipment-list li {
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .equipment-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nueva Solicitud de Insumos</h1>
    </div>
    
    <div class="content">
        <p>Hola,</p>
        
        <p>Se ha recibido una nueva solicitud de insumos en el sistema INFRASTOCK.</p>
        
        <div class="info-box">
            <div class="info-row">
                <span class="label">Solicitud #:</span> {{ $request->id }}
            </div>
            <div class="info-row">
                <span class="label">Solicitante:</span> {{ $userName }}
            </div>
            <div class="info-row">
                <span class="label">Rol:</span> {{ $roleName }}
            </div>
            <div class="info-row">
                <span class="label">Total de Insumos:</span> {{ $totalItems }}
            </div>
            <div class="info-row">
                <span class="label">Fecha:</span> {{ $request->created_at->format('d/m/Y H:i') }}
            </div>
            @if($request->description)
            <div class="info-row">
                <span class="label">Descripción:</span> {{ $request->description }}
            </div>
            @endif
        </div>
        
        <h3>Insumos Solicitados:</h3>
        <ul class="equipment-list">
            @foreach($request->items as $item)
            <li>
                <strong>{{ $item->equipment->name }}</strong> - 
                Cantidad: {{ $item->requested_amount }}
                @if($item->equipment->category)
                    ({{ $item->equipment->category->name }})
                @endif
            </li>
            @endforeach
        </ul>
        
        <p style="margin-top: 20px;">
            <a href="{{ route('infrastock.admin.requests.index') }}" class="button">
                Ver Solicitud en el Sistema
            </a>
        </p>
    </div>
    
    <div class="footer">
        <p>Este es un correo automático del sistema INFRASTOCK.</p>
        <p>Por favor, no responda a este correo.</p>
    </div>
</body>
</html>

