<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\TemplateProcessor;
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

        $template = base_path('context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx');
        @ini_set('memory_limit', '512M');
        $spreadsheet = SpreadsheetFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();
        [$headerRow, $columns] = $this->studentWorkbookColumns($sheet);
        $dataStart = $headerRow + 1;
        $templateRow = $dataStart;

        if ($students->count() > 1) {
            $sheet->insertNewRowBefore($dataStart + 1, $students->count() - 1);
        }

        foreach ($students as $index => $student) {
            $row = $dataStart + $index;

            if ($row !== $templateRow) {
                $sheet->duplicateStyle($sheet->getStyle($templateRow), "{$row}:{$row}");
            }

            foreach ($this->studentWorkbookRow($student, $index) as $key => $value) {
                if (isset($columns[$key])) {
                    $sheet->setCellValue([$columns[$key], $row], $value);
                }
            }
        }

        $highestRow = $sheet->getHighestRow();
        $clearFrom = $dataStart + $students->count();

        for ($row = $clearFrom; $row <= $highestRow; $row++) {
            foreach ($columns as $column) {
                $sheet->setCellValue([$column, $row], null);
            }
        }

        return $this->spreadsheetContent($spreadsheet);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function reportCardsDoc(array $filters = []): string
    {
        return $this->reportCardsDocx($filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function reportCardsDocx(array $filters = []): string
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

        if ($reportCards->isEmpty()) {
            return $this->plainDocx('No report cards matched the selected filters.');
        }

        if ($reportCards->count() === 1) {
            return $this->reportCardDocx($reportCards->first());
        }

        $paragraphs = $reportCards->map(fn (ReportCard $reportCard): array => [
            'STUDENT EVALUATION',
            'NAME: '.$this->reportCardValue($reportCard, 'student_name'),
            'CLASS: '.$this->reportCardValue($reportCard, 'class_name'),
            'TOTAL SCORE: '.$this->reportCardValue($reportCard, 'total_score').'/100',
            'FINAL GRADE: '.$this->reportCardValue($reportCard, 'final_grade'),
            'Comments and Suggestions: '.$this->reportCardValue($reportCard, 'comments'),
        ])->flatten()->all();

        return $this->plainDocx($paragraphs);
    }

    public function reportCardDocx(ReportCard $reportCard): string
    {
        $reportCard->loadMissing('enrollment.user', 'enrollment.courseClass', 'instructor', 'academicDirector', 'managingDirector');

        $templatePath = resource_path('document-templates/report-card-template.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('Report card DOCX template is missing.');
        }

        $processor = new TemplateProcessor($templatePath);

        foreach ($this->reportCardValues($reportCard) as $key => $value) {
            $processor->setValue($key, $value);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'etc-report-card-');

        if ($tmp === false) {
            throw new RuntimeException('Unable to create temporary report card document.');
        }

        $docx = $tmp.'.docx';

        try {
            $processor->saveAs($docx);
            $content = file_get_contents($docx);

            if ($content === false) {
                throw new RuntimeException('Unable to read generated report card document.');
            }

            return $content;
        } finally {
            @unlink($tmp);
            @unlink($docx);
        }
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
     * @return array<string, string>
     */
    public function reportCardValues(ReportCard $reportCard): array
    {
        $student = $reportCard->enrollment?->user;
        $class = $reportCard->enrollment?->courseClass;

        return [
            'student_name' => $student?->full_name ?? $student?->name ?? '-',
            'class_name' => $class?->name ?? '-',
            'class_days' => $class?->schedule_days ?? '-',
            'class_time' => $class?->schedule_time ?? '-',
            'score_listening' => (string) ($reportCard->score_listening ?? '-'),
            'score_vocabulary' => (string) ($reportCard->score_vocabulary ?? '-'),
            'score_structure' => (string) ($reportCard->score_structure ?? '-'),
            'score_reading' => (string) ($reportCard->score_reading ?? '-'),
            'score_writing' => (string) ($reportCard->score_writing ?? '-'),
            'grade_pronunciation' => $reportCard->grade_pronunciation ?? '-',
            'grade_sentence_arrangement' => $reportCard->grade_sentence_arrangement ?? '-',
            'grade_class_participation' => $reportCard->grade_class_participation ?? '-',
            'grade_questioning_skill' => $reportCard->grade_questioning_skill ?? '-',
            'grade_analyzing_skill' => $reportCard->grade_analyzing_skill ?? '-',
            'total_score' => (string) ($reportCard->total_score ?? '-'),
            'final_grade' => $reportCard->final_grade ?? '-',
            'next_class' => $reportCard->next_class ?? '-',
            'comments' => $reportCard->comments ?? '-',
            'issued_at' => $reportCard->issued_at?->format('Y-m-d') ?? now()->toDateString(),
            'managing_director_name' => $reportCard->managingDirector?->name ?? '-',
            'academic_director_name' => $reportCard->academicDirector?->name ?? '-',
            'instructor_name' => $reportCard->instructor?->name ?? '-',
        ];
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

    private function spreadsheetContent(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'etc-xlsx-');

        if ($tmp === false) {
            throw new RuntimeException('Unable to create XLSX temporary file.');
        }

        try {
            (new Xlsx($spreadsheet))->save($tmp);
            $content = file_get_contents($tmp);

            if ($content === false) {
                throw new RuntimeException('Unable to read generated XLSX archive.');
            }

            return $content;
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * @return array{0: int, 1: array<string, int>}
     */
    private function studentWorkbookColumns(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        $highestRow = min($sheet->getHighestRow(), 40);
        $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($row = 1; $row <= $highestRow; $row++) {
            $columns = [];

            for ($column = 1; $column <= $highestColumn; $column++) {
                $label = strtoupper(trim((string) $sheet->getCell([$column, $row])->getFormattedValue()));
                $key = match (true) {
                    str_contains($label, 'NO INDUK') => 'no_induk',
                    $label === 'NO' || str_contains($label, 'NO.') => 'number',
                    str_contains($label, 'NAME') || str_contains($label, 'NAMA') => 'name',
                    str_contains($label, 'CLASS') || str_contains($label, 'KELAS') => 'class',
                    str_contains($label, 'SEX M') => 'sex_m',
                    str_contains($label, 'SEX F') => 'sex_f',
                    str_contains($label, 'BIRTH PLACE') || str_contains($label, 'TEMPAT') => 'birth_place',
                    str_contains($label, 'BIRTH DATE') || str_contains($label, 'TANGGAL LAHIR') => 'birth_date',
                    str_contains($label, 'STATUS') => 'status',
                    str_contains($label, 'ADDRESS') || str_contains($label, 'ALAMAT') => 'address',
                    str_contains($label, 'TGL DAFTAR') || str_contains($label, 'DAFTAR') => 'registered_at',
                    str_contains($label, 'CONTACT') || str_contains($label, 'PHONE') || str_contains($label, 'HP') => 'contact',
                    str_contains($label, 'PHOTO') || str_contains($label, 'FOTO') => 'photo',
                    str_contains($label, 'KET') || str_contains($label, 'NOTE') => 'notes',
                    default => null,
                };

                if ($key) {
                    $columns[$key] = $column;
                }
            }

            if (isset($columns['no_induk'], $columns['name'])) {
                return [$row, $columns];
            }
        }

        throw new RuntimeException('Student recap template header row with NO INDUK and NAME was not found.');
    }

    /**
     * @return array<string, string>
     */
    private function studentWorkbookRow(User $student, int $index): array
    {
        $enrollment = $student->enrollments->sortByDesc('enrolled_at')->first();
        $registration = $student->registrations->sortByDesc('created_at')->first();

        return [
            'number' => (string) ($index + 1),
            'no_induk' => $student->no_induk ?? '',
            'name' => $student->full_name ?? $student->name,
            'class' => $enrollment?->courseClass?->name ?? $registration?->courseClass?->name ?? '',
            'sex_m' => $student->sex === 'M' ? 'M' : '',
            'sex_f' => $student->sex === 'F' ? 'F' : '',
            'birth_place' => $student->place_of_birth ?? '',
            'birth_date' => $student->date_of_birth?->format('Y-m-d') ?? '',
            'status' => $student->status ?? '',
            'address' => $student->address ?? '',
            'registered_at' => $registration?->created_at?->format('Y-m-d') ?? $student->created_at?->format('Y-m-d') ?? '',
            'contact' => $student->mobile_phone ?? $registration?->applicant_phone ?? '',
            'photo' => $student->avatar ?? '',
            'notes' => $registration?->notes ?? '',
        ];
    }

    private function reportCardValue(ReportCard $reportCard, string $key): string
    {
        return $this->reportCardValues($reportCard)[$key] ?? '-';
    }

    /**
     * @param  string|array<int, string>|Collection<int, string>  $paragraphs
     */
    private function plainDocx(string|array|Collection $paragraphs): string
    {
        $paragraphs = collect(is_string($paragraphs) ? [$paragraphs] : $paragraphs)
            ->map(fn (string $text): string => '<w:p><w:r><w:t>'.e($text).'</w:t></w:r></w:p>')
            ->implode('');

        $tmp = tempnam(sys_get_temp_dir(), 'etc-docx-');

        if ($tmp === false) {
            throw new RuntimeException('Unable to create DOCX archive.');
        }

        $zip = new ZipArchive;

        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to open DOCX archive.');
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'.$paragraphs.'<w:sectPr/></w:body></w:document>');
        $zip->close();

        $content = file_get_contents($tmp);
        @unlink($tmp);

        if ($content === false) {
            throw new RuntimeException('Unable to read generated DOCX archive.');
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
