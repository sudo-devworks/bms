<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BackupReportExport implements WithMultipleSheets
{
    public function __construct(
        protected Builder $query,
        protected array $filters = [],
        protected ?Collection $pendingJobs = null
    ) {}

    public function sheets(): array
    {
        return [
            new BackupReportDetailSheet(clone $this->query),
            new BackupReportSummarySheet(
                clone $this->query,
                $this->filters,
                $this->pendingJobs ?? collect()
            ),
        ];
    }
}