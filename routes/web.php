<?php

Route::get('/', function () {
    return view('registro'); 
});

Route::get('/registro', function () {
    return view('registro');
});

Route::post('/cadastrar', [App\Http\Controllers\RegistroController::class, 'salvar']);