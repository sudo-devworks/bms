<?php

namespace App\Exports;

use App\Models\BackupLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BackupReportDetailSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        protected Builder $query
    ) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function title(): string
    {
        return 'Backup Report';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Sistem',
            'Job Code',
            'Job Name',
            'Storage',
            'Status',
            'File Name',
            'File Path',
            'File Size',
            'Duration',
            'Started At',
            'Finished At',
            'Message',
            'Error Message',
        ];
    }

    /**
     * @param BackupLog $log
     */
    public function map($log): array
    {
        return [
            $log->backup_date?->format('Y-m-d'),
            $log->system?->name,
            $log->job?->code,
            $log->job?->name,
            $log->storage?->name,
            $log->statusLabel(),
            $log->file_name,
            $log->file_path,
            $log->fileSizeLabel(),
            $log->durationLabel(),
            $log->started_at?->format('Y-m-d H:i:s'),
            $log->finished_at?->format('Y-m-d H:i:s'),
            $log->message,
            $log->error_message,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:N'.$highestRow);

                $sheet->getStyle('A1:N1')->getFont()->setBold(true);

                $sheet->getStyle('M:N')->getAlignment()->setWrapText(true);

                $sheet->getStyle('A:N')->getAlignment()->setVertical('top');
            },
        ];
    }
}