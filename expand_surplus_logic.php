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
        continue;
    }

    $content = file_get_contents($path);

    // Update surplusReport to include approved requests
    $content = preg_replace(
        '/->where\(\'status\', \'delivered\'\)\s*->orderBy\(\'created_at\', \'desc\'\)\s*->get\(\);/',
        "->whereIn('status', ['approved', 'delivered'])\n            ->orderBy('created_at', 'desc')\n            ->get();",
        $content
    );

    // Update storeSurplus validation to include approved requests
    $content = preg_replace(
        '/->where\(\'status\', \'delivered\'\)\s*->first\(\);/',
        "->whereIn('status', ['approved', 'delivered'])\n                ->first();",
        $content
    );

    file_put_contents($path, $content);
    echo "Patched $filename\n";
}
