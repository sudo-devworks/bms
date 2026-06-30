<?php

namespace App\Exports;

use App\Models\BackupLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BackupReportSummarySheet implements FromCollection, WithTitle, ShouldAutoSize, WithStrictNullComparison, WithEvents
{
    protected Collection $pendingJobs;

    public function __construct(
        protected Builder $query,
        protected array $filters = [],
        ?Collection $pendingJobs = null
    ) {
        $this->pendingJobs = $pendingJobs ?? collect();
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function collection(): Collection
    {
        $summaryRow = (clone $this->query)
            ->reorder()
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END), 0) as success,
                COALESCE(SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END), 0) as failed,
                COALESCE(SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END), 0) as warning,
                COALESCE(SUM(file_size_bytes), 0) as total_size_bytes,
                AVG(duration_seconds) as avg_duration_seconds
            ')
            ->first();

        $dateFrom = $this->filters['date_from'] ?? now()->toDateString();
        $dateTo = $this->filters['date_to'] ?? now()->toDateString();

        $total = (int) ($summaryRow->total ?? 0);
        $success = (int) ($summaryRow->success ?? 0);
        $warning = (int) ($summaryRow->warning ?? 0);
        $failed = (int) ($summaryRow->failed ?? 0);
        $pending = $this->pendingJobs->count();
        $totalSizeBytes = (int) ($summaryRow->total_size_bytes ?? 0);

        $rows = collect([
            ['Backup Report Summary'],
            [''],
            ['Periode Mulai', $this->formatDate($dateFrom)],
            ['Periode Selesai', $this->formatDate($dateTo)],
            ['Tanggal Export', now()->format('d M Y H:i:s')],
            ['Timezone', config('app.timezone')],
            [''],
            ['Total Log', $total],
            ['Success', $success],
            ['Warning', $warning],
            ['Failed', $failed],
            ['Pending', $pending],
            ['Total Ukuran Backup', $this->formatBytes($totalSizeBytes)],
            ['Rata-rata Durasi', $this->formatDuration($summaryRow->avg_duration_seconds ?? null)],
            [''],
            ['Catatan'],
            ['Laporan ini membaca data dari database backup_logs.'],
            ['Pending bukan log aktual. Pending adalah job aktif yang belum memiliki log pada tanggal cek.'],
            ['BMS tidak scan folder backup, tidak menjalankan backup, dan tidak membuat scheduler.'],
            [''],
            ['Pending Jobs'],
            ['No', 'Sistem', 'Job Code', 'Job Name', 'Frekuensi', 'Jam Ekspektasi', 'Catatan'],
        ]);

        if ($this->pendingJobs->count()) {
            foreach ($this->pendingJobs as $index => $job) {
                $rows->push([
                    $index + 1,
                    $job->system?->name ?? '-',
                    $job->code,
                    $job->name,
                    method_exists($job, 'frequencyLabel') ? $job->frequencyLabel() : ($job->expected_frequency ?? '-'),
                    $job->expected_time ? $job->expected_time->format('H:i') : '-',
                    'Belum ada log masuk ke BMS pada tanggal '.$this->formatDate($dateTo).'.',
                ]);
            }
        } else {
            $rows->push([
                1,
                '-',
                '-',
                'Tidak ada pending job',
                '-',
                '-',
                'Semua job aktif sudah memiliki log pada tanggal '.$this->formatDate($dateTo).'.',
            ]);
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:G1');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');

                $sheet->getRowDimension(1)->setRowHeight(24);

                $sheet->getStyle('A8:A14')->getFont()->setBold(true);
                $sheet->getStyle('A16')->getFont()->setBold(true);
                $sheet->getStyle('A21')->getFont()->setBold(true);
                $sheet->getStyle('A22:G22')->getFont()->setBold(true);

                $sheet->getStyle('A:G')->getAlignment()->setVertical('top');
                $sheet->getStyle('G:G')->getAlignment()->setWrapText(true);

                if ($highestRow >= 22) {
                    $sheet->setAutoFilter('A22:G'.$highestRow);
                }
            },
        ];
    }

    private function formatDate(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->format('d M Y');
    }

    private function formatBytes(?int $bytes): string
    {
        if (!$bytes) {
            return '-';
        }

        $size = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, $index === 0 ? 0 : 2).' '.$units[$index];
    }

    private function formatDuration($seconds): string
    {
        if ($seconds === null) {
            return '-';
        }

        $seconds = (int) round($seconds);

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d jam %d menit %d detik', $hours, $minutes, $seconds);
        }

        if ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $seconds);
        }

        return sprintf('%d detik', $seconds);
    }
}