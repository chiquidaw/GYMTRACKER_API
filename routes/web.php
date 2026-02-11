<?php

use Illuminate\Support\Facades\Route;

// Página informativa con listado de rutas de la API
Route::get('/', function () {
    return view('api-docs');
});
