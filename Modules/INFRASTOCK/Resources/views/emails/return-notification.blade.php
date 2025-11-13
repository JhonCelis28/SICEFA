<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Devolución de Insumos</title>
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
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background-color: white;
            border-left: 4px solid #10B981;
            padding: 15px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #10B981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nueva Devolución de Insumos</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $admin->name }}</strong>,</p>
        
        <p>Se ha registrado una nueva devolución de insumos que requiere tu revisión:</p>
        
        <div class="info-box">
            <p><strong>Usuario:</strong> {{ $userName }}</p>
            <p><strong>Insumo:</strong> {{ $equipmentName }}</p>
            <p><strong>Cantidad a Devolver:</strong> {{ $returnMovement->amount }} {{ $returnMovement->equipment->unit ?? 'unidades' }}</p>
            <p><strong>ID Solicitud:</strong> #{{ $surplus->request_id }}</p>
            @if($returnMovement->description)
                <p><strong>Descripción:</strong> {{ $returnMovement->description }}</p>
            @endif
        </div>
        
        <p>Por favor, revisa esta devolución en el módulo de Préstamos y Devoluciones para aprobarla o rechazarla.</p>
        
        <a href="{{ route('infrastock.admin.loans.index') }}" class="button">
            Ver Devoluciones
        </a>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático del sistema INFRASTOCK.</p>
        <p>© {{ date('Y') }} INFRASTOCK - Todos los derechos reservados.</p>
    </div>
</body>
</html>

