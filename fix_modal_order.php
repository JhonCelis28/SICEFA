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

    // 1. Extract the JSON block
    if (preg_match('/\$acceptHeader = request\(\)->header\(\'Accept\', \'\'\);.*?\}\s*\n\n/s', $content, $matches)) {
        $jsonBlock = $matches[0];
        // 2. Remove it from its current position
        $content = str_replace($jsonBlock, '', $content);
        
        // 3. Find where the model is loaded
        // It looks like: $request = InfrastockRequest::with([...])->...->first();
        if (preg_match('/\$request = [a-zA-Z0-9_]+::with\(.*?\)->first\(\);/s', $content, $m2)) {
            $modelLoading = $m2[0];
            // 4. Re-insert after model loading and the null check
            // Find the if (!$request) { ... } block
            if (preg_match('/' . preg_quote($modelLoading, '/') . '\s*\n\s*if\s*\(\!\$request\)\s*\{.*?\}\s*\n/s', $content, $m3)) {
                $content = str_replace($m3[0], $m3[0] . "\n" . $jsonBlock, $content);
                file_put_contents($path, $content);
                echo "Fixed $filename\n";
            } else {
                 echo "Could not find null check for $filename\n";
            }
        } else {
            echo "Could not find model loading for $filename\n";
        }
    } else {
        echo "Could not find JSON block for $filename\n";
    }
}
