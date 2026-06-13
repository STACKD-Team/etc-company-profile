<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;
use ZipArchive;

class DocumentExportService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function studentWorkbook(array $filters = []): string
    {
        $students = User::query()
            ->students()
            ->with(['enrollments.courseClass.program', 'registrations.program', 'registrations.courseClass'])
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['class_id'] ?? null, function (Builder $query, int|string $classId) {
                $query->whereHas('enrollments', fn (Builder $query) => $query->where('class_id', $classId));
            })
            ->when($filters['program_id'] ?? null, function (Builder $query, int|string $programId) {
                $query->whereHas('enrollments.courseClass', fn (Builder $query) => $query->where('program_id', $programId));
            })
            ->when($filters['year'] ?? null, function (Builder $query, int|string $year) {
                $query->whereYear('created_at', $year);
            })
            ->orderBy('full_name')
            ->orderBy('name')
            ->get();

        $rows = [[
            'NO',
            'NO INDUK',
            'NAME',
            'CLASS',
            'SEX M',
            'SEX F',
            'BIRTH PLACE',
            'BIRTH DATE',
            'STATUS',
            'PLACE/ADDRESS',
            'TGL DAFTAR',
            'CONTACT PERSON',
            'PHOTO',
            'KET',
        ]];

        foreach ($students as $index => $student) {
            $enrollment = $student->enrollments->sortByDesc('enrolled_at')->first();
            $registration = $student->registrations->sortByDesc('created_at')->first();

            $rows[] = [
                (string) ($index + 1),
                $student->no_induk ?? '',
                $student->full_name ?? $student->name,
                $enrollment?->courseClass?->name ?? $registration?->courseClass?->name ?? '',
                $student->sex === 'M' ? 'M' : '',
                $student->sex === 'F' ? 'F' : '',
                $student->place_of_birth ?? '',
                $student->date_of_birth?->format('Y-m-d') ?? '',
                $student->status ?? '',
                $student->address ?? '',
                $registration?->created_at?->format('Y-m-d') ?? $student->created_at?->format('Y-m-d') ?? '',
                $student->mobile_phone ?? $registration?->applicant_phone ?? '',
                $student->avatar ?? '',
                $registration?->notes ?? '',
            ];
        }

        return $this->xlsx($rows);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function reportCardsDoc(array $filters = []): string
    {
        $reportCards = ReportCard::query()
            ->with(['enrollment.user', 'enrollment.courseClass', 'instructor', 'academicDirector', 'managingDirector'])
            ->when($filters['report_card_id'] ?? null, fn (Builder $query, int|string $id) => $query->whereKey($id))
            ->when($filters['class_id'] ?? null, function (Builder $query, int|string $classId) {
                $query->whereHas('enrollment', fn (Builder $query) => $query->where('class_id', $classId));
            })
            ->when(array_key_exists('is_published', $filters) && $filters['is_published'] !== null && $filters['is_published'] !== '', fn (Builder $query) => $query->where('is_published', (bool) $filters['is_published']))
            ->when($filters['issued_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('issued_at', '>=', $date))
            ->when($filters['issued_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('issued_at', '<=', $date))
            ->latest('issued_at')
            ->latest()
            ->get();

        $sections = $reportCards->map(fn (ReportCard $reportCard) => $this->reportCardHtml($reportCard))->implode('<br style="page-break-after: always;">');

        if ($sections === '') {
            $sections = '<p>No report cards matched the selected filters.</p>';
        }

        return '<html><head><meta charset="utf-8"><style>'.$this->docStyles().'</style></head><body>'.$sections.'</body></html>';
    }

    public function reportCardHtml(ReportCard $reportCard): string
    {
        $student = $reportCard->enrollment?->user;
        $class = $reportCard->enrollment?->courseClass;

        return view('pages.admin.placement-test.partials.report-card-document', [
            'reportCard' => $reportCard,
            'studentName' => $student?->full_name ?? $student?->name ?? '-',
            'className' => $class?->name ?? '-',
            'classDays' => $class?->schedule_days ?? '-',
            'classTime' => $class?->schedule_time ?? '-',
            'instructorName' => $reportCard->instructor?->name ?? '-',
            'academicDirectorName' => $reportCard->academicDirector?->name ?? '-',
            'managingDirectorName' => $reportCard->managingDirector?->name ?? '-',
        ])->render();
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function xlsx(array $rows): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZipArchive extension is required to generate XLSX exports.');
        }

        $sheet = $this->worksheetXml($rows);
        $tmp = tempnam(sys_get_temp_dir(), 'etc-xlsx-');
        $zip = new ZipArchive;

        if ($tmp === false || $zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create XLSX archive.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->close();

        $content = file_get_contents($tmp);
        @unlink($tmp);

        if ($content === false) {
            throw new RuntimeException('Unable to read generated XLSX archive.');
        }

        return $content;
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function worksheetXml(array $rows): string
    {
        $xmlRows = collect($rows)->map(function (array $row, int $index) {
            $rowNumber = $index + 1;
            $cells = collect($row)->map(function (string $value, int $column) use ($rowNumber) {
                $cell = $this->columnName($column + 1).$rowNumber;

                return '<c r="'.$cell.'" t="inlineStr"><is><t>'.e($value).'</t></is></c>';
            })->implode('');

            return '<row r="'.$rowNumber.'">'.$cells.'</row>';
        })->implode('');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$xmlRows.'</sheetData></worksheet>';
    }

    private function columnName(int $column): string
    {
        $name = '';

        while ($column > 0) {
            $mod = ($column - 1) % 26;
            $name = chr(65 + $mod).$name;
            $column = intdiv($column - $mod, 26);
        }

        return $name;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts><fills count="1"><fill><patternFill patternType="none"/></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="1"><xf/></cellXfs></styleSheet>';
    }

    private function docStyles(): string
    {
        return 'body{font-family:Arial,sans-serif;color:#111;font-size:11pt}.report-card{width:100%;max-width:720px;margin:0 auto}.title{text-align:center;font-size:18pt;font-weight:bold;letter-spacing:.04em}.identity,.scores,.signatures{width:100%;border-collapse:collapse;margin-top:14px}.identity td,.scores th,.scores td{border:1px solid #111;padding:6px}.scores th{background:#f2f2f2}.section-title{margin-top:18px;font-weight:bold}.comments{border:1px solid #111;min-height:70px;padding:8px}.signatures td{text-align:center;padding-top:42px}.muted{color:#555}';
    }
}
