<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BackupStorageController;
use App\Http\Controllers\BackupSystemController;
use App\Http\Controllers\BackupJobController;
use App\Http\Controllers\BackupLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorageUsageController;
use App\Http\Controllers\BackupReportController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\BackupAlertController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/monitoring', [MonitoringController::class, 'index'])
    ->name('monitoring.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('backup-storages', BackupStorageController::class);

    Route::patch(
        'backup-storages/{backupStorage}/toggle-status',
        [BackupStorageController::class, 'toggleStatus']
    )->name('backup-storages.toggle-status');

    Route::resource('backup-systems', BackupSystemController::class);

    Route::patch(
        'backup-systems/{backupSystem}/toggle-status',
        [BackupSystemController::class, 'toggleStatus']
    )->name('backup-systems.toggle-status');

    Route::resource('backup-jobs', BackupJobController::class);

    Route::patch(
        'backup-jobs/{backupJob}/toggle-status',
        [BackupJobController::class, 'toggleStatus']
    )->name('backup-jobs.toggle-status');

    Route::resource('backup-logs', BackupLogController::class)->except(['edit', 'update']);

    Route::get('/storage-usage', [StorageUsageController::class, 'index'])
    ->name('storage-usage.index');

    Route::get('/storage-usage/{backupStorage}/edit', [StorageUsageController::class, 'edit'])
    ->name('storage-usage.edit');

    Route::patch('/storage-usage/{backupStorage}', [StorageUsageController::class, 'update'])
        ->name('storage-usage.update');

    Route::get('/backup-reports', [BackupReportController::class, 'index'])
        ->name('backup-reports.index');

    Route::get('/backup-reports/export', [BackupReportController::class, 'export'])
        ->name('backup-reports.export');

    Route::get('/backup-alerts', [BackupAlertController::class, 'index'])
        ->name('backup-alerts.index');

    Route::patch('/backup-alerts/{backupAlert}/resolve', [BackupAlertController::class, 'resolve'])
        ->name('backup-alerts.resolve');

    Route::patch('/backup-alerts/{backupAlert}/ignore', [BackupAlertController::class, 'ignore'])
        ->name('backup-alerts.ignore');

    Route::post('/backup-alerts/recipients', [BackupAlertController::class, 'storeRecipient'])
        ->name('backup-alerts.recipients.store');

    Route::patch('/backup-alerts/recipients/{notificationSetting}/toggle', [BackupAlertController::class, 'toggleRecipient'])
        ->name('backup-alerts.recipients.toggle');

    Route::delete('/backup-alerts/recipients/{notificationSetting}', [BackupAlertController::class, 'deleteRecipient'])
        ->name('backup-alerts.recipients.delete');

    Route::post('/backup-alerts/test-email', [BackupAlertController::class, 'sendTestEmail'])
        ->name('backup-alerts.test-email');

    Route::post('/backup-alerts/send-pending-emails', [BackupAlertController::class, 'sendPendingEmails'])
        ->name('backup-alerts.send-pending-emails');

    Route::post('/backup-alerts/retry-failed-emails', [BackupAlertController::class, 'retryFailedEmails'])
        ->name('backup-alerts.retry-failed-emails');
});

require __DIR__.'/auth.php';
