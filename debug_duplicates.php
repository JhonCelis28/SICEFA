<?php
// Load Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\INFRASTOCK\Entities\Equipment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

echo "--- INSPECTING EQUIPMENTS TABLE ---\n";
try {
    $supplies = Equipment::where('name', 'like', 'Jabon%')->get();
    echo "Found " . $supplies->count() . " records starting with 'Jabon':\n";
    foreach ($supplies as $s) {
        echo "ID: {$s->id}, Name: '{$s->name}', CategoryID: {$s->category_id}, DeletedAt: {$s->deleted_at}\n";
    }
}
catch (\Exception $e) {
    echo "ERROR during inspection: " . $e->getMessage() . "\n";
}

echo "\n--- TESTING VALIDATION LOGIC ---\n";
$testName = "Jabon liquido"; // Existing name from screenshot

$data = ['name' => $testName];
$rules = [
    'name' => [
        'required',
        Rule::unique('equipments', 'name')->whereNull('deleted_at')
    ]
];

$validator = Validator::make($data, $rules);

if ($validator->fails()) {
    echo "Validation FAILS (Correct) for name '$testName':\n";
    print_r($validator->errors()->all());
}
else {
    echo "Validation PASSES (FAILED) for name '$testName'. UNEXPECTED!\n";
}

echo "\n--- TESTING VALIDATION WITH IGNORE ---\n";
if ($supplies->count() > 0) {
    $id = $supplies->first()->id;
    $validator = Validator::make($data, [
        'name' => [
            Rule::unique('equipments', 'name')->ignore($id)->whereNull('deleted_at')
        ]
    ]);
    if ($validator->fails()) {
        echo "Validation FAILS (Unexpected) when ignoring own ID $id\n";
    }
    else {
        echo "Validation PASSES (Correct) when ignoring own ID $id\n";
    }
}
