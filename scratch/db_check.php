<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\User;

$output = "Total Categories: " . Category::count() . "\n";
$output .= "Categories List:\n";
foreach (Category::all() as $cat) {
    $output .= "- ID: {$cat->id}, Name: {$cat->name}\n";
}

$output .= "\nTotal Users: " . User::count() . "\n";
$user = User::latest()->first();
$output .= "Last User: " . ($user ? $user->email : 'None') . " (Verified: " . ($user && $user->email_verified_at ? 'Yes' : 'No') . ")\n";

file_put_contents(__DIR__ . '/db_results.txt', $output);
echo "Results written to db_results.txt\n";
