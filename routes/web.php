<?php

declare(strict_types=1);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/json', 'Controller@index');
