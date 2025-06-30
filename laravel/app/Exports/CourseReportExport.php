<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $completionReport;
    protected $popularityReport;
    protected $activityReport;

    public function __construct(Collection $completionReport, Collection $popularityReport, Collection $activityReport)
    {
        $this->completionReport = $completionReport;
        $this->popularityReport = $popularityReport;
        $this->activityReport = $activityReport;
    }

    public function collection()
    {
        $data = new Collection();

        // Add Completion Report data
        $data->push(['課程完成度報告']);
        $data->push([]);
        $data->push($this->headings());
        foreach ($this->completionReport as $item) {
            $data->push($item);
        }

        $data->push([]); // Add empty row
        $data->push(['課程熱門度報告']);
        $data->push([]);
        $data->push(['課程', '指派次數']);
        foreach ($this->popularityReport as $item) {
            $data->push($item);
        }
        
        $data->push([]); // Add empty row
        $data->push(['學員活躍度報告']);
        $data->push([]);
        $data->push(['日期', '活躍學員數']);
        foreach ($this->activityReport as $item) {
            $data->push($item);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            '課程',
            '完成率 (%)',
        ];
    }
    
    public function map($row): array
    {
        return $row;
    }
}
