<?php
$roleControllers = [
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

    // 1. Regex to find the JSON block at the beginning of showRequest
    $jsonPattern = '/(\$acceptHeader = request\(\)->header\(\'Accept\', \'\'\);.*?\}\s*\n\n)/s';
    if (preg_match($jsonPattern, $content, $matches)) {
        $jsonBlock = $matches[1];
        $content = str_replace($jsonBlock, '', $content);
        
        // 2. Regex to find the null check in showRequest
        $nullCheckPattern = '/(abort\(404, \'Solicitud no encontrada\'\);\s*\}\s*\n)/s';
        if (preg_match($nullCheckPattern, $content, $m2)) {
            $nullCheck = $m2[1];
            $content = str_replace($nullCheck, $nullCheck . "\n" . $jsonBlock, $content);
            file_put_contents($path, $content);
            echo "Successfully moved block in $filename\n";
        } else {
             echo "Failed to find null check in $filename\n";
        }
    } else {
        echo "Failed to find JSON block in $filename\n";
    }
}
echo "Done.\n";
