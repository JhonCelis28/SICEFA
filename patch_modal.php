<?php
$controllerDir = __DIR__ . '/Modules/INFRASTOCK/Http/Controllers/';
$controllers = glob($controllerDir . '*.php');

foreach ($controllers as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        
        // Let's replace the inner map function returned array
        $pattern = '/\'items\'\s*=>\s*\$request->items->map\(function\(\$item\)\s*\{\s*return\s*\[(.*?)\];\s*\}\)/s';
        
        $replacement = <<< 'EOD'
'items' => $request->items->map(function($item) {
                    $returned = \Modules\INFRASTOCK\Entities\Surplus::where('request_item_id', $item->id)
                        ->where('status', 'approved')
                        ->sum('surplus_amount');

                    $approved = $item->approved_amount ?? (in_array($item->status, ['approved', 'delivered']) ? $item->requested_amount : 0);
                    $delivered = $item->delivered_amount ?? ($item->status === 'delivered' ? $item->requested_amount : 0);

                    return [
                        'equipment_name' => $item->equipment->name ?? 'N/A',
                        'equipment_category' => $item->equipment->category->name ?? 'Sin categoría',
                        'status' => $item->status,
                        'requested_amount' => $item->requested_amount,
                        'approved_amount' => $approved,
                        'delivered_amount' => $delivered,
                        'returned_amount' => $returned,
                        'unit' => $item->equipment->unit_measure ?? $item->equipment->unit ?? 'unidades',
                        'notes' => $item->notes,
                    ];
                })
EOD;

        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Patched controller: " . basename($file) . PHP_EOL;
        }
    }
}

// Now patch the views
$viewDir = __DIR__ . '/Modules/INFRASTOCK/Resources/views/';
$views = glob($viewDir . '*/my-requests.blade.php');

$viewPattern = '/<div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mt-3">(.*?)<\/div>\s*\$\{item\.notes/s';

$viewReplacement = <<< 'EOD'
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm mt-3">
                            <div class="bg-gray-50 p-2 rounded">
                                <label class="block text-xs font-medium text-gray-500">Solicitada</label>
                                <p class="text-gray-900 font-medium">${item.requested_amount} ${item.unit}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <label class="block text-xs font-medium text-gray-500">Aprobada</label>
                                <p class="text-gray-900 font-medium">${item.approved_amount} ${item.unit}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <label class="block text-xs font-medium text-gray-500">Entregada</label>
                                <p class="text-gray-900 font-medium">${item.delivered_amount} ${item.unit}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <label class="block text-xs font-medium text-gray-500">Devuelta</label>
                                <p class="text-gray-900 font-medium">${item.returned_amount} ${item.unit}</p>
                            </div>
                        </div>
                        ${item.notes
EOD;

foreach ($views as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $newContent = preg_replace($viewPattern, $viewReplacement, $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Patched view: " . basename(dirname($file)) . "/" . basename($file) . PHP_EOL;
        }
    }
}
echo "Done." . PHP_EOL;
