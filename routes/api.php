<?php

use App\Http\Controllers\Api\BackupLogReceiverController;
use App\Http\Controllers\Api\StorageStatusController;
use Illuminate\Support\Facades\Route;

Route::post('/backup/logs', [BackupLogReceiverController::class, 'store'])
    ->name('api.backup-logs.store');
Route::post('/storage/status', [StorageStatusController::class, 'store'])
    ->name('api.storage.status');