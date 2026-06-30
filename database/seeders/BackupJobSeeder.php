<?php

namespace Database\Seeders;

use App\Models\BackupJob;
use App\Models\BackupSystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BackupJobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'system_codes' => ['TRACER_STUDY', 'ALUMNI_STIP', 'TRACER'],
                'system_keywords' => ['tracer', 'alumni'],
                'name' => 'Backup Database Tracer Study Harian',
                'code' => 'BACKUP_TRACER_STUDY_DB_DAILY',
                'backup_type' => BackupJob::TYPE_DATABASE,
                'schedule_text' => 'Backup database Tracer Study via cron server setiap malam.',
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'expected_time' => '23:00:00',
                'notes' => 'Job monitoring untuk hasil backup database Tracer Study. BMS hanya mencatat log, bukan menjalankan backup.',
            ],
            [
                'system_codes' => ['KEUANGAN', 'FINANCE', 'FDB_KEUANGAN'],
                'system_keywords' => ['keuangan', 'finance'],
                'name' => 'Backup Database Keuangan Harian',
                'code' => 'BACKUP_KEUANGAN_DB_DAILY',
                'backup_type' => BackupJob::TYPE_DATABASE,
                'schedule_text' => 'Backup database Keuangan via cron server setiap malam.',
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'expected_time' => '23:30:00',
                'notes' => 'Job monitoring untuk kumpulan database Keuangan/FDB. Detail file dapat dicatat di Backup Log.',
            ],
            [
                'system_codes' => ['DIKLAT_PELAUT', 'SMILE_KIDS', 'DIKLAT'],
                'system_keywords' => ['diklat', 'pelaut', 'smile'],
                'name' => 'Backup File Diklat Pelaut Harian',
                'code' => 'BACKUP_DIKLAT_PELAUT_FILE_DAILY',
                'backup_type' => BackupJob::TYPE_FILE,
                'schedule_text' => 'Copy file backup Diklat Pelaut ke storage tujuan setiap hari.',
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'expected_time' => '23:45:00',
                'notes' => 'Job monitoring file hasil backup aplikasi Diklat Pelaut.',
            ],
            [
                'system_codes' => ['EPRALA'],
                'system_keywords' => ['eprala'],
                'name' => 'Backup Database EPrala Harian',
                'code' => 'BACKUP_EPRALA_DB_DAILY',
                'backup_type' => BackupJob::TYPE_DATABASE,
                'schedule_text' => 'Backup database EPrala via cron server setiap malam.',
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'expected_time' => '23:50:00',
                'notes' => 'Job monitoring untuk database EPrala.',
            ],
            [
                'system_codes' => ['OJS', 'OJS_STIP', 'JOURNAL'],
                'system_keywords' => ['ojs', 'journal', 'jurnal'],
                'name' => 'Backup OJS Database dan Files',
                'code' => 'BACKUP_OJS_MIXED_DAILY',
                'backup_type' => BackupJob::TYPE_MIXED,
                'schedule_text' => 'Backup database dan file OJS sesuai script existing.',
                'expected_frequency' => BackupJob::FREQUENCY_DAILY,
                'expected_time' => '22:30:00',
                'notes' => 'Job monitoring gabungan database dan files OJS.',
            ],
            [
                'system_codes' => ['ZIMBRA', 'MAIL', 'EMAIL'],
                'system_keywords' => ['zimbra', 'mail', 'email'],
                'name' => 'Backup Zimbra Manual',
                'code' => 'BACKUP_ZIMBRA_MAIL_MANUAL',
                'backup_type' => BackupJob::TYPE_MAIL,
                'schedule_text' => 'Backup mail dilakukan manual atau sesuai kebutuhan.',
                'expected_frequency' => BackupJob::FREQUENCY_MANUAL,
                'expected_time' => null,
                'notes' => 'Job monitoring untuk backup Zimbra/mail. Non-harian dan tidak dihitung sebagai kewajiban harian jika dinonaktifkan.',
            ],
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($jobs as $jobData) {
            $system = $this->findSystem($jobData['system_codes'], $jobData['system_keywords']);

            if (! $system) {
                $skipped++;
                $this->command?->warn('Skip job '.$jobData['code'].' karena sistem backup belum ditemukan.');
                continue;
            }

            $job = BackupJob::updateOrCreate(
                ['code' => $jobData['code']],
                [
                    'backup_system_id' => $system->id,
                    'name' => $jobData['name'],
                    'backup_type' => $jobData['backup_type'],
                    'schedule_text' => $jobData['schedule_text'],
                    'expected_frequency' => $jobData['expected_frequency'],
                    'expected_time' => $jobData['expected_time'],
                    'is_active' => true,
                    'notes' => $jobData['notes'],
                ]
            );

            $job->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->command?->info("BackupJobSeeder selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    private function findSystem(array $codes, array $keywords): ?BackupSystem
    {
        $normalizedCodes = collect($codes)
            ->map(fn ($code) => Str::of($code)->upper()->replaceMatches('/[^A-Z0-9]+/', '_')->replaceMatches('/_+/', '_')->trim('_')->toString())
            ->filter()
            ->values()
            ->all();

        $system = BackupSystem::query()
            ->whereIn('code', $normalizedCodes)
            ->first();

        if ($system) {
            return $system;
        }

        return BackupSystem::query()
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%'.$keyword.'%')
                        ->orWhere('code', 'like', '%'.Str::upper($keyword).'%');
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->first();
    }
}
