<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Category Count: " . \App\Models\Category::count() . "\n";
echo "User Count: " . \App\Models\User::count() . "\n";
echo "First Category: " . json_encode(\App\Models\Category::first()) . "\n";
