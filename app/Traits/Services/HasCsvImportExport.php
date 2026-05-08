<?php

namespace App\Traits\Services;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasCsvImportExport
{
    abstract protected function getExportColumns(): array;

    abstract protected function getExportFilename(): string;

    abstract protected function getTemplateFilename(): string;

    abstract protected function getTemplateRows(): array;

    abstract protected function writeExportRows($handle): void;

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $this->getExportColumns());
            $this->writeExportRows($handle);
            fclose($handle);
        }, $this->getExportFilename(), ['Content-Type' => 'text/csv']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $this->getExportColumns());
            foreach ($this->getTemplateRows() as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $this->getTemplateFilename(), ['Content-Type' => 'text/csv']);
    }

    /**
     * Opens and validates the import CSV header row.
     * Returns [$handle, null] on success, or [null, $result] if the file is invalid
     * so the caller can immediately return the error result.
     */
    protected function openValidatedCsvImport(UploadedFile $file, string $translationNamespace): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);

            return [null, ['imported' => 0, 'failed' => 0, 'errors' => [__("{$translationNamespace}.import_empty_file")]]];
        }

        if (array_map('trim', $headers) !== $this->getExportColumns()) {
            fclose($handle);

            return [null, ['imported' => 0, 'failed' => 0, 'errors' => [__("{$translationNamespace}.import_invalid_headers")]]];
        }

        return [$handle, null];
    }
}
