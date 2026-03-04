<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$eq = \Modules\INFRASTOCK\Entities\Equipment::first();
echo json_encode($eq->toArray());
