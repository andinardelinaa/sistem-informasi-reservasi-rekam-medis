<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$indexes = DB::select('SHOW INDEX FROM jadwal');

foreach ($indexes as $index) {
    echo "Key: {$index->Key_name}, Column: {$index->Column_name}, Non_unique: {$index->Non_unique}\n";
}