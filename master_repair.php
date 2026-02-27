<?php
$roleControllers = [
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

foreach ($roleControllers as $filename) {
    $path = $basePath . $filename;
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);

    // 1. Re-apply notifyAdminNewRequest fix (Remove emails, fix performance)
    $roleName = str_replace('Controller.php', '', $filename);
    $roleLabel = [
        'Agroindustria' => 'Agroindustria',
        'Operator' => 'Operador',
        'Vigilancia' => 'Vigilancia',
        'Psicola' => 'Psicola',
        'Ganaderia' => 'Ganadería',
        'Convivencia' => 'Convivencia',
        'CienciasBasicas' => 'Ciencias Básicas',
        'CleaningStaff' => 'Personal de Aseo'
    ][$roleName] ?? $roleName;

    $cleanMethod = "    private function notifyAdminNewRequest(\$request)\n    {\n        try {\n            \$admins = User::whereHas('roles', function(\$query) {\n                \$query->where('slug', 'infrastock.admin')\n                      ->orWhere('slug', 'superadmin')\n                      ->orWhere('name', 'Administrador')\n                      ->orWhere('name', 'Super Administrador');\n            })->get();\n\n            \$totalItems = \$request->items->count();\n            \$equipmentNames = \$request->items->pluck('equipment.name')->toArray();\n            \$equipmentList = implode(', ', array_slice(\$equipmentNames, 0, 3));\n            if (count(\$equipmentNames) > 3) {\n                \$equipmentList .= ' y ' . (count(\$equipmentNames) - 3) . ' más';\n            }\n\n            \$userName = \$request->user->nickname ?? \$request->user->name ?? '$roleLabel';\n\n            foreach (\$admins as \$admin) {\n                Notification::create([\n                    'type' => 'request_created',\n                    'notifiable_type' => 'App\Models\User',\n                    'notifiable_id' => \$admin->id,\n                    'data' => [\n                        'title' => 'Nueva Solicitud de Insumos',\n                        'message' => \"$roleLabel ha creado una nueva solicitud con {\$totalItems} insumos: {\$equipmentList}.\",\n                        'request_id' => \$request->id,\n                        'total_items' => \$totalItems,\n                        'equipment_list' => \$equipmentList,\n                        'user_name' => \$userName,\n                        'action_url' => route('infrastock.admin.supply-requests.index'),\n                        'created_at' => now()->format('d/m/Y H:i'),\n                    ],\n                ]);\n            }\n            Log::info('Notificación enviada a ' . \$admins->count() . ' administradores para solicitud #' . \$request->id);\n        } catch (\\Exception \$e) {\n            Log::error('Error enviando notificación al administrador: ' . \$e->getMessage());\n        }\n    }";

    $content = preg_replace('/private function notifyAdminNewRequest\(\$request\)\s*\{.*?\}\s*(\r?\n\s*)(\/\*\*|private function notifyAdminSurplus)/s', $cleanMethod . "\n\n    $2", $content);

    // 2. Re-apply showRequest AJAX/JSON support (Modal details)
    $jsonBlock = "        \$acceptHeader = request()->header('Accept', '');\n        if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || strpos(\$acceptHeader, 'application/json') !== false) {\n            return response()->json([\n                'id' => \$request->id,\n                'status' => \$request->status,\n                'created_at' => \$request->created_at->format('d/m/Y H:i'),\n                'description' => \$request->description ?? '',\n                'productive_unit' => \$request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A',\n                'warehouse' => \$request->productiveUnitWarehouse->warehouse->name ?? 'N/A',\n                'user' => \$request->user->nickname ?? \$request->user->name ?? 'N/A',\n                'items' => \$request->items->map(function(\$item) {\n                    return [\n                        'equipment_name' => \$item->equipment->name ?? 'N/A',\n                        'equipment_category' => \$item->equipment->category->name ?? 'N/A',\n                        'status' => \$item->status,\n                        'requested_amount' => \$item->requested_amount,\n                        'approved_amount' => \$item->approved_amount ?? 0,\n                        'delivered_amount' => \$item->delivered_amount ?? 0,\n                        'returned_amount' => \Modules\INFRASTOCK\Entities\Surplus::where('request_item_id', \$item->id)->sum('surplus_amount'),\n                        'unit' => \$item->equipment->unit_measure ?? \$item->equipment->unit ?? 'unidades',\n                        'notes' => \$item->notes ?? '',\n                    ];\n                })\n            ]);\n        }\n\n";

    $lines = explode("\n", $content);
    $newLines = [];
    $applied = false;
    foreach ($lines as $line) {
        $newLines[] = $line;
        if (preg_match('/public function showRequest\(\$id\)/', $line)) {
            $inShowRequest = true;
        }
        if (isset($inShowRequest) && $inShowRequest && preg_match('/\{/', $line) && !$applied) {
            $newLines[] = $jsonBlock;
            $applied = true;
            $inShowRequest = false;
        }
    }
    $content = implode("\n", $newLines);

    // 3. Re-apply Surplus logic improvements for Agroindustria and Operator
    if ($filename == 'AgroindustriaController.php' || $filename == 'OperatorController.php') {
        // Dropdown: allow approved and delivered
        $content = preg_replace(
            '/->where\(\'status\', \'delivered\'\)\s*->orderBy\(\'created_at\', \'desc\'\)\s*->get\(\);/',
            "->whereIn('status', ['approved', 'delivered'])\n            ->orderBy('created_at', 'desc')\n            ->get();",
            $content
        );
        // Storage: validate against approved and delivered
        $content = preg_replace(
            '/->where\(\'status\', \'delivered\'\)\s*->first\(\);/',
            "->whereIn('status', ['approved', 'delivered'])\n                ->first();",
            $content
        );
    }

    file_put_contents($path, $content);
    echo "Successfully updated $filename\n";
}

// 4. Admin Controllers: Prevent zero-sum surplus creation
foreach (['AdminRequestController.php', 'SupplyRequestController.php'] as $adm) {
    $admPath = $basePath . $adm;
    if (file_exists($admPath)) {
        $content = file_get_contents($admPath);
        // Find and remove the automatic surplus creation blocks
        $content = preg_replace('/\/\/\s*Crear automáticamente un registro de sobrante con cantidad 0.*?Surplus::create\(.*?\);/s', '', $content);
        file_put_contents($admPath, $content);
        echo "Successfully updated $adm\n";
    }
}

// 5. Instructor Controller: Tool return validation
$instructorPath = $basePath . 'InstructorController.php';
if (file_exists($instructorPath)) {
    $content = file_get_contents($instructorPath);
    // Add validation to returnTool
    $content = preg_replace('/(\$request->validate\(\[)/', "$1\n            'amount' => 'required|integer|min:1',", $content);
    // Add business logic to prevent over-returning
    $logicBlock = "        if (\$request->amount > (\$loan->amount - \$loan->returned_amount)) {\n            return redirect()->back()->with('error', 'La cantidad a devolver supera lo pendiente.');\n        }\n";
    $content = preg_replace('/(\$loan = WarehouseMovement::find\(\$id\);)/', "$1\n$logicBlock", $content);
    file_put_contents($instructorPath, $content);
    echo "Successfully updated InstructorController.php\n";
}

echo "Done.\n";
