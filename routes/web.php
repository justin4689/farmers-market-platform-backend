<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/swagger/index.html'));
