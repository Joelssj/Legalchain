<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

    Route::get('/check-db', function () {
        try {
            DB::connection()->getPdo();
            return 'Successfully connected to the database: ' . DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            return 'Failed to connect to the database: ' . $e->getMessage();
        }
    });
    