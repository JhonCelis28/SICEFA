<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \Illuminate\Http\Request();
$request->merge([
    'first_name' => 'Juan',
    'first_last_name' => 'Perez',
    'document_type' => '1',
    'document_number' => '999888877',
    'email' => 'juanp12@infrastock.local',
    'role_id' => Modules\SICA\Entities\Role::where('app_id', 19)->first()->id,
    'is_active' => '1',
]);
$controller = new Modules\INFRASTOCK\Http\Controllers\UserManagementController();
$response = $controller->store($request);
if ($response->isRedirect()) {
    $session = session()->all();
    if (isset($session['errors'])) {
        print_r($session['errors']->toArray());
    } else {
        echo "Success redirect!\n";
    }
} else {
    echo "Not a redirect.\n";
}
