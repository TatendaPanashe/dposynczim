<?php

namespace App\Services;

use App\Models\RopaRecord;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

final class RopaExcelExporter
{
    /** @param Collection<int, RopaRecord> $records */
    public function export(Collection $records): string
    {
        $template = storage_path('app/templates/dpa-ropa-template.xlsx');

        if (! is_file($template)) {
            throw new RuntimeException('The ROPA template file is missing.');
        }

        $output = tempnam(sys_get_temp_dir(), 'ropa-');

        if ($output === false || ! copy($template, $output)) {
            throw new RuntimeException('Could not prepare the ROPA export.');
        }

        $zip = new ZipArchive();

        if ($zip->open($output) !== true) {
            throw new RuntimeException('Could not open the ROPA template.');
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        if ($sheetXml === false) {
            $zip->close();

            throw new RuntimeException('The ROPA template worksheet is missing.');
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', $this->replaceRows($sheetXml, $records));
        $zip->close();

        $content = file_get_contents($output);
        @unlink($output);

        if ($content === false) {
            throw new RuntimeException('Could not read the ROPA export.');
        }

        return $content;
    }

    /** @param Collection<int, RopaRecord> $records */
    private function replaceRows(string $sheetXml, Collection $records): string
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = false;
        $document->loadXML($sheetXml);

        $namespace = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('x', $namespace);

        $sheetData = $xpath->query('/x:worksheet/x:sheetData')->item(0);

        if (! $sheetData instanceof \DOMElement) {
            throw new RuntimeException('The ROPA template worksheet data is missing.');
        }

        $styleByColumn = $this->styleByColumn($xpath);

        foreach (iterator_to_array($xpath->query('x:row[@r > 1]', $sheetData)) as $row) {
            $sheetData->removeChild($row);
        }

        $rowNumber = 2;

        foreach ($records->values() as $index => $record) {
            $sheetData->appendChild($this->row($document, $namespace, $rowNumber, $this->values($record, $index + 1), $styleByColumn));
            $rowNumber++;
        }

        $dimension = $xpath->query('/x:worksheet/x:dimension')->item(0);

        if ($dimension instanceof \DOMElement) {
            $dimension->setAttribute('ref', 'A1:AF'.max(1, $rowNumber - 1));
        }

        return $document->saveXML();
    }

    /** @return array<string, string> */
    private function styleByColumn(\DOMXPath $xpath): array
    {
        $styles = [];

        foreach ($xpath->query('//x:row[@r="2"]/x:c') as $cell) {
            if (! $cell instanceof \DOMElement || ! $cell->hasAttribute('s')) {
                continue;
            }

            $styles[preg_replace('/\d+/', '', $cell->getAttribute('r'))] = $cell->getAttribute('s');
        }

        return $styles;
    }

    /** @param array<int, string|int|null> $values */
    private function row(\DOMDocument $document, string $namespace, int $rowNumber, array $values, array $styleByColumn): \DOMElement
    {
        $row = $document->createElementNS($namespace, 'row');
        $row->setAttribute('r', (string) $rowNumber);
        $row->setAttribute('spans', '1:32');

        foreach ($values as $offset => $value) {
            $column = $this->columnName($offset + 1);
            $cell = $document->createElementNS($namespace, 'c');
            $cell->setAttribute('r', $column.$rowNumber);

            if (isset($styleByColumn[$column])) {
                $cell->setAttribute('s', $styleByColumn[$column]);
            }

            if (is_int($value)) {
                $cell->appendChild($document->createElementNS($namespace, 'v', (string) $value));
            } elseif ($value !== null && $value !== '') {
                $cell->setAttribute('t', 'inlineStr');
                $inline = $document->createElementNS($namespace, 'is');
                $text = $document->createElementNS($namespace, 't');
                $text->appendChild($document->createTextNode((string) $value));
                $inline->appendChild($text);
                $cell->appendChild($inline);
            }

            $row->appendChild($cell);
        }

        return $row;
    }

    /** @return array<int, string|int|null> */
    private function values(RopaRecord $record, int $rowNumber): array
    {
        return [
            $rowNumber,
            $record->business_function,
            $record->storage_location,
            $record->processing_activity,
            $record->purpose,
            $record->legal_basis,
            $record->owner,
            $record->controller_name ?: $record->organization?->name,
            $record->representative_name,
            $this->join($record->data_subject_categories),
            $record->retention_period,
            $record->retention_basis,
            $record->data_classification ?: $this->join($record->personal_data_categories),
            $this->join($record->recipients),
            $record->processor_name,
            $record->third_party_agreement,
            $this->join($record->security_measures),
            $record->transfer_security_measures ?: $this->join($record->cross_border_transfer),
            $record->protection_assessment,
            $record->data_collection_method,
            $record->consent_evidence,
            $record->legitimate_interest_assessment,
            $record->data_volume,
            $record->dpia_record,
            $record->data_risks,
            $record->risk_impact,
            $record->data_breaches,
            $record->breach_notification,
            $record->risk_actions,
            $record->action_owner,
            $record->action_due_date?->format('Y-m-d'),
            $record->reviewed_at?->format('Y-m-d'),
        ];
    }

    /** @param array<int, string>|null $items */
    private function join(?array $items): ?string
    {
        if ($items === null || $items === []) {
            return null;
        }

        return implode('; ', $items);
    }

    private function columnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }
}
