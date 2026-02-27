<?php
// universal_fix.php - Ensures total consistency across all role controllers

$controllersDir = 'Modules/INFRASTOCK/Http/Controllers';
$controllers = [
    'AgroindustriaController.php',
    'VigilanciaController.php',
    'PsicolaController.php',
    'OperatorController.php',
    'GanaderiaController.php',
    'ConvivenciaController.php',
    'CienciasBasicasController.php',
    'CleaningStaffController.php'
];

foreach ($controllers as $file) {
    $path = "$controllersDir/$file";
    if (!file_exists($path)) continue;

    echo "Standardizing $file...\n";
    $content = file_get_contents($path);

    // 1. Ensure surplusReport uses ['approved', 'delivered']
    $content = preg_replace(
        "/where\(\s*'status'\s*,\s*'delivered'\s*\)/",
        "whereIn('status', ['approved', 'delivered'])",
        $content
    );

    // 2. Fix validation messages
    $content = str_replace(
        "no ha sido entregada.",
        "no está entregada o aprobada.",
        $content
    );

    // 3. Ensure returned_amount calculation is consistent in items map
    // Look for 'unit' => ... and ensure 'returned_amount' is present before it
    if (strpos($content, "'returned_amount'") === false && strpos($content, "'items' => \$request->items->map") !== false) {
        $content = preg_replace(
            "/('delivered_amount'\s*=>\s*.*?,)/",
            "$1\n                        'returned_amount' => \\Modules\\INFRASTOCK\\Entities\\Surplus::where('request_item_id', \$item->id)->sum('surplus_amount'),",
            $content
        );
    }

    file_put_contents($path, $content);
    
    // Syntax check
    exec("php -l \"$path\"", $output, $returnVar);
    if ($returnVar !== 0) {
        echo "SYNTAX ERROR in $file after universal fix\n";
    }
}

echo "Universal standardization completed.\n";
