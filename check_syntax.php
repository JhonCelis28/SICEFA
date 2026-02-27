<?php
$dir = 'Modules/INFRASTOCK/Http/Controllers';
$files = glob("$dir/*.php");
$errors = [];

foreach ($files as $file) {
    echo "Checking $file... ";
    $output = [];
    $returnVar = 0;
    exec("php -l \"$file\" 2>&1", $output, $returnVar);
    if ($returnVar !== 0) {
        $errors[] = implode("\n", $output);
        echo "FAILED\n";
    } else {
        echo "OK\n";
    }
}

if (!empty($errors)) {
    echo "\nFound syntax errors:\n";
    echo implode("\n---\n", $errors);
    exit(1);
} else {
    echo "\nAll files passed syntax check.\n";
    exit(0);
}
