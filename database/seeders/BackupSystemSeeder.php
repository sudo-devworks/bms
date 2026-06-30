<?php

namespace Database\Seeders;

use App\Models\BackupStorage;
use App\Models\BackupSystem;
use Illuminate\Database\Seeder;

class BackupSystemSeeder extends Seeder
{
    public function run(): void
    {
        $storage = BackupStorage::query()
            ->where('is_active', true)
            ->first();

        if (! $storage) {
            $this->command?->warn('Seeder backup systems dilewati: belum ada storage aktif.');
            return;
        }

        $systems = [
            [
                'name' => 'Tracer Study',
                'code' => 'TRACER_STUDY',
                'category' => BackupSystem::CATEGORY_DATABASE,
                'source_server' => 'Server Tracer Study',
                'source_path' => 'ALUMNI_STIP.FDB, JAKARTA.FDB, CRB.FDB',
                'backup_schedule' => 'Setiap hari pukul 00:00',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup database Firebird Tracer Study.',
            ],
            [
                'name' => 'Keuangan',
                'code' => 'KEUANGAN',
                'category' => BackupSystem::CATEGORY_DATABASE,
                'source_server' => 'Server Keuangan',
                'source_path' => 'SSO.FDB, EPLANNING.FDB, BELANJA.FDB, PENERIMAAN.FDB, ASET.FDB, DKKP.FDB, BIOS.FDB, PJM.FDB, EREVISI.FDB, SMART_CAMPUS.FDB, KAPAL_LATIH.FDB',
                'backup_schedule' => 'Setiap hari pukul 00:00',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup database Firebird sistem keuangan.',
            ],
            [
                'name' => 'EPrala',
                'code' => 'EPRALA',
                'category' => BackupSystem::CATEGORY_DATABASE,
                'source_server' => 'Server EPrala',
                'source_path' => '/home/trb/firebird/EPRALA.FDB',
                'backup_schedule' => 'Setiap hari pukul 00:00',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup database Firebird EPrala.',
            ],
            [
                'name' => 'OJS',
                'code' => 'OJS',
                'category' => BackupSystem::CATEGORY_MIXED,
                'source_server' => 'Server OJS',
                'source_path' => 'Database dan file aplikasi OJS',
                'backup_schedule' => 'Setiap hari pukul 23:30',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup database dan file upload/journal OJS.',
            ],
            [
                'name' => 'Zimbra',
                'code' => 'ZIMBRA',
                'category' => BackupSystem::CATEGORY_MAIL,
                'source_server' => 'Server Mail Zimbra',
                'source_path' => '/opt/zimbra',
                'backup_schedule' => 'Manual / sesuai maintenance',
                'expected_frequency' => BackupSystem::FREQUENCY_MANUAL,
                'notes' => 'Backup mail server. Detail mekanisme akan disesuaikan kondisi server mail.',
            ],
            [
                'name' => 'PMB',
                'code' => 'PMB',
                'category' => BackupSystem::CATEGORY_MIXED,
                'source_server' => 'Server PMB',
                'source_path' => 'Database dan source aplikasi PMB',
                'backup_schedule' => 'Setiap hari pukul 23:30',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup aplikasi dan database PMB.',
            ],
            [
                'name' => 'Dashboard Manajerial',
                'code' => 'DASHBOARD_MANAJERIAL',
                'category' => BackupSystem::CATEGORY_MIXED,
                'source_server' => 'Server Dashboard Manajerial',
                'source_path' => 'Database dan source aplikasi dashboard',
                'backup_schedule' => 'Setiap hari pukul 23:30',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup aplikasi dashboard manajerial.',
            ],
            [
                'name' => 'Diklat Pelaut',
                'code' => 'DIKLAT_PELAUT',
                'category' => BackupSystem::CATEGORY_FILE,
                'source_server' => 'Server Diklat Pelaut',
                'source_path' => '/var/www/html/smile-kids/app/webroot/files/bckp_0db1_st1p/',
                'backup_schedule' => 'Setiap hari sesuai cron existing',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup file zip existing dari folder backup aplikasi Diklat Pelaut.',
            ],
            [
                'name' => 'SLIMS',
                'code' => 'SLIMS',
                'category' => BackupSystem::CATEGORY_MIXED,
                'source_server' => 'Server SLIMS',
                'source_path' => 'Database dan file SLIMS',
                'backup_schedule' => 'Setiap hari pukul 23:30',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup sistem perpustakaan SLIMS.',
            ],
            [
                'name' => 'Absensi Catar',
                'code' => 'ABSENSI_CATAR',
                'category' => BackupSystem::CATEGORY_MIXED,
                'source_server' => 'Server Absensi Catar',
                'source_path' => 'Database dan source aplikasi absensi',
                'backup_schedule' => 'Setiap hari pukul 23:30',
                'expected_frequency' => BackupSystem::FREQUENCY_DAILY,
                'notes' => 'Backup aplikasi Absensi Catar.',
            ],
        ];

        foreach ($systems as $system) {
            BackupSystem::updateOrCreate(
                ['code' => $system['code']],
                array_merge($system, [
                    'backup_storage_id' => $storage->id,
                    'is_active' => true,
                ])
            );
        }
    }
}