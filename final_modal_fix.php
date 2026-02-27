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
    if ($filename == 'AgroindustriaController.php') continue; // Already fixed manually

    $path = $basePath . $filename;
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);

    // 1. Extract and remove the JSON block from the top of showRequest
    $jsonMarker = '$acceptHeader = request()->header(\'Accept\', \'\');';
    if (strpos($content, $jsonMarker) !== false) {
        // Find the start and end of the JSON block
        $startPos = strpos($content, $jsonMarker);
        
        // Find the matching close brace for the 'if' block
        // It's usually return response()->json([...]);\n        }\n\n
        $endMarker = "]);\n        }\n";
        $endPos = strpos($content, $endMarker, $startPos);
        
        if ($endPos !== false) {
            $endPos += strlen($endMarker);
            $jsonBlock = substr($content, $startPos, $endPos - $startPos);
            
            // Remove the block
            $content = substr_replace($content, '', $startPos, $endPos - $startPos);
            
            // 2. Find the model loading and re-insert after null check
            $nullCheckMarker = "abort(404, 'Solicitud no encontrada');\n        }";
            if (strpos($content, $nullCheckMarker) !== false) {
                $insertPos = strpos($content, $nullCheckMarker) + strlen($nullCheckMarker);
                $content = substr_replace($content, "\n\n" . $jsonBlock, $insertPos, 0);
                
                file_put_contents($path, $content);
                echo "Successfully corrected $filename\n";
            } else {
                echo "Could not find null check marker in $filename\n";
            }
        } else {
            echo "Could not find end of JSON block in $filename\n";
        }
    } else {
        echo "JSON block already moved or not found in $filename\n";
    }
}
echo "Fix completed.\n";
