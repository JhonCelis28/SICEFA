<?php
$controllers = [
    'AgroindustriaController.php',
    'OperatorController.php',
    'VigilanciaController.php',
    'PsicolaController.php',
    'GanaderiaController.php',
    'ConvivenciaController.php',
    'CienciasBasicasController.php',
    'CleaningStaffController.php'
];

$basePath = 'c:/laragon/www/sicefados/Modules/INFRASTOCK/Http/Controllers/';

foreach ($controllers as $filename) {
    $path = $basePath . $filename;
    if (!file_exists($path)) {
        echo "File $path not found.\n";
        continue;
    }

    $content = file_get_contents($path);

    // 1. Remove email notification logic from notifyAdminNewRequest
    // We replace the entire method with a clean version to avoid brace issues
    $roleName = str_replace('Controller.php', '', $filename);
    if ($roleName == 'Agroindustria') $roleLabel = 'Agroindustria';
    else if ($roleName == 'Operator') $roleLabel = 'Operador';
    else if ($roleName == 'Vigilancia') $roleLabel = 'Vigilancia';
    else if ($roleName == 'Psicola') $roleLabel = 'Psicola';
    else if ($roleName == 'Ganaderia') $roleLabel = 'Ganadería';
    else if ($roleName == 'Convivencia') $roleLabel = 'Convivencia';
    else if ($roleName == 'CienciasBasicas') $roleLabel = 'Ciencias Básicas';
    else if ($roleName == 'CleaningStaff') $roleLabel = 'Personal de Aseo';
    else $roleLabel = $roleName;

    $cleanMethod = "    private function notifyAdminNewRequest(\$request)\n    {\n        try {\n            \$admins = User::whereHas('roles', function(\$query) {\n                \$query->where('slug', 'infrastock.admin')\n                      ->orWhere('slug', 'superadmin')\n                      ->orWhere('name', 'Administrador')\n                      ->orWhere('name', 'Super Administrador');\n            })->get();\n\n            \$totalItems = \$request->items->count();\n            \$equipmentNames = \$request->items->pluck('equipment.name')->toArray();\n            \$equipmentList = implode(', ', array_slice(\$equipmentNames, 0, 3));\n            if (count(\$equipmentNames) > 3) {\n                \$equipmentList .= ' y ' . (count(\$equipmentNames) - 3) . ' más';\n            }\n\n            \$userName = \$request->user->nickname ?? \$request->user->name ?? '$roleLabel';\n\n            foreach (\$admins as \$admin) {\n                Notification::create([\n                    'type' => 'request_created',\n                    'notifiable_type' => 'App\\Models\\User',\n                    'notifiable_id' => \$admin->id,\n                    'data' => [\n                        'title' => 'Nueva Solicitud de Insumos',\n                        'message' => \"$roleLabel ha creado una nueva solicitud con {\$totalItems} insumos: {\$equipmentList}.\",\n                        'request_id' => \$request->id,\n                        'total_items' => \$totalItems,\n                        'equipment_list' => \$equipmentList,\n                        'user_name' => \$userName,\n                        'action_url' => route('infrastock.admin.supply-requests.index'),\n                        'created_at' => now()->format('d/m/Y H:i'),\n                    ],\n                ]);\n            }\n            Log::info('Notificación enviada a ' . \$admins->count() . ' administradores para solicitud #' . \$request->id);\n        } catch (\\Exception \$e) {\n            Log::error('Error enviando notificación al administrador: ' . \$e->getMessage());\n        }\n    }";

    $content = preg_replace('/private function notifyAdminNewRequest\(\$request\)\s*\{.*?\}\s*(\r?\n\s*)(\/\*\*|private function notifyAdminSurplus)/s', $cleanMethod . "\n\n    $2", $content);

    // 2. Fix showRequest to handle AJAX/JSON
    $jsonBlock = "        \$acceptHeader = request()->header('Accept', '');
        if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || strpos(\$acceptHeader, 'application/json') !== false) {
            return response()->json([
                'id' => \$request->id,
                'status' => \$request->status,
                'created_at' => \$request->created_at->format('d/m/Y H:i'),
                'description' => \$request->description ?? '',
                'productive_unit' => \$request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A',
                'warehouse' => \$request->productiveUnitWarehouse->warehouse->name ?? 'N/A',
                'user' => \$request->user->nickname ?? \$request->user->name ?? 'N/A',
                'items' => \$request->items->map(function(\$item) {
                    return [
                        'equipment_name' => \$item->equipment->name ?? 'N/A',
                        'equipment_category' => \$item->equipment->category->name ?? 'N/A',
                        'status' => \$item->status,
                        'requested_amount' => \$item->requested_amount,
                        'approved_amount' => \$item->approved_amount ?? 0,
                        'delivered_amount' => \$item->delivered_amount ?? 0,
                        'returned_amount' => \Modules\INFRASTOCK\Entities\Surplus::where('request_item_id', \$item->id)->sum('surplus_amount'),
                        'unit' => \$item->equipment->unit_measure ?? \$item->equipment->unit ?? 'unidades',
                        'notes' => \$item->notes ?? '',
                    ];
                })
            ]);
        }\n\n";

    $lines = explode("\n", $content);
    $newLines = [];
    $inShowRequest = false;
    $applied = false;

    foreach ($lines as $line) {
        if (preg_match('/public function showRequest\(\$id\)/', $line)) {
            $inShowRequest = true;
        }

        if ($inShowRequest && preg_match('/return view\(.*?, compact\(\'request\'\)\);/', $line) && !$applied) {
            $newLines[] = $jsonBlock;
            $applied = true;
            $inShowRequest = false; 
        }

        $newLines[] = $line;
        
        if ($inShowRequest && preg_match('/public function \w+/', $line) && !preg_match('/showRequest/', $line)) {
            $inShowRequest = false;
        }
    }

    $content = implode("\n", $newLines);

    if (file_put_contents($path, $content)) {
        echo "Successfully patched $filename\n";
    } else {
        echo "Error patching $filename\n";
    }
}
echo "Done.\n";
