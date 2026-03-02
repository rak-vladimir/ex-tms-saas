<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json([
    'name'   => 'Mini TMS API',
    'status' => 'active',
    'php'    => PHP_VERSION,
]));
