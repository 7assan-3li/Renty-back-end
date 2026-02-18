<?php
foreach (\App\Models\Car::all() as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Status: {$c->status}" . PHP_EOL;
}
