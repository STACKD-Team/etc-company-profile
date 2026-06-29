<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;
use PhpOffice\PhpWord\IOFactory as WordFactory;
use Smalot\PdfParser\Parser as PdfParser;

class TextExtractionService
{
    public function extract(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');
        $path = $file->getRealPath();

        if (! $path) {
            return '';
        }

        return match ($extension) {
            'pdf' => trim((new PdfParser())->parseFile($path)->getText()),
            'doc', 'docx' => $this->extractWord($path),
            'xls', 'xlsx', 'csv' => $this->extractSpreadsheet($path),
            default => trim((string) file_get_contents($path)),
        };
    }

    protected function extractWord(string $path): string
    {
        $document = WordFactory::load($path);
        $parts = [];

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $parts[] = (string) $element->getText();
                }
            }
        }

        return trim(implode("\n", $parts));
    }

    protected function extractSpreadsheet(string $path): string
    {
        $spreadsheet = SpreadsheetFactory::load($path);
        $parts = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->toArray(null, true, true, true) as $row) {
                $line = trim(implode(' ', array_filter(array_map('strval', $row))));

                if ($line !== '') {
                    $parts[] = $line;
                }
            }
        }

        return trim(implode("\n", $parts));
    }
}
