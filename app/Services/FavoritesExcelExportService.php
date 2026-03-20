<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class FavoritesExcelExportService
{
    private const array COLUMN_WIDTHS = [
        'A' => 16,
        'B' => 19.11,
        'C' => 13,
        'D' => 12.78,
        'E' => 14.44,
        'F' => 18.11,
        'G' => 15.33,
        'H' => 18.55,
        'I' => 15.22,
    ];

    private const array HEADER_LABELS = [
        'A3' => 'Irodaház neve',
        'B3' => 'Irodaház címe',
        'C3' => 'Építés éve',
        'D3' => 'Bérleti díj',
        'E3' => 'Üzemeltetési díj',
        'F3' => 'Közös területi arány',
        'G3' => 'Parkolóhely díja',
        'H3' => 'Min. bérleti időszak',
        'I3' => 'Ajánlott terület',
    ];

    /**
     * Generate an Excel file from a collection of properties.
     *
     * @param  Collection  $properties  Collection of Property models
     */
    public function generate(Collection $properties): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('munkafüzet');

        $this->setColumnWidths($sheet);
        $this->setTitleRow($sheet);
        $this->setHeaderRow($sheet);
        $this->setDataRows($sheet, $properties);

        $tempFile = tempnam(sys_get_temp_dir(), 'favorites_export_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $tempFile;
    }

    private function setColumnWidths(Worksheet $sheet): void
    {
        foreach (self::COLUMN_WIDTHS as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function setTitleRow(Worksheet $sheet): void
    {
        $sheet->setCellValue('B1', 'PSG-IRODAHAZAK.HU');
        $sheet->mergeCells('B1:H1');

        $titleStyle = $sheet->getStyle('B1');
        $titleStyle->getFont()
            ->setBold(true)
            ->setSize(11)
            ->setName('Calibri');

        $titleStyle->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Blue theme color (theme=4, tint=-0.5) - approximated as dark blue
        $titleStyle->getFont()->getColor()->setARGB('FF1F4E79');

        // Row 2: empty with height 15
        $sheet->getRowDimension(2)->setRowHeight(15);
    }

    private function setHeaderRow(Worksheet $sheet): void
    {
        foreach (self::HEADER_LABELS as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $headerRange = 'A3:I3';

        $headerStyle = $sheet->getStyle($headerRange);
        $headerStyle->getFont()
            ->setBold(true)
            ->setSize(11)
            ->setName('Calibri');

        $headerStyle->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $this->applyMediumBorders($sheet, $headerRange);
    }

    private function setDataRows(Worksheet $sheet, Collection $properties): void
    {
        $row = 4;

        foreach ($properties as $property) {
            $sheet->setCellValue('A'.$row, $property->title ?? '');
            $sheet->setCellValue('B'.$row, $this->formatAddress($property));
            $sheet->setCellValue('C'.$row, $property->construction_year ?? '');
            $sheet->setCellValue('D'.$row, $this->formatRentalFee($property));
            $sheet->setCellValue('E'.$row, $this->formatOperatingFee($property));
            $sheet->setCellValue('F'.$row, $this->formatCommonAreaRatio($property));
            $sheet->setCellValue('G'.$row, $this->formatParkingFee($property));
            $sheet->setCellValue('H'.$row, $this->formatMinRentalPeriod($property));
            // Column I (Ajánlott terület) is left empty intentionally

            $dataRange = sprintf('A%d:I%d', $row, $row);

            $dataStyle = $sheet->getStyle($dataRange);
            $dataStyle->getFont()
                ->setBold(false)
                ->setSize(11)
                ->setName('Calibri');

            $dataStyle->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $this->applyMediumBorders($sheet, $dataRange);

            $row++;
        }
    }

    private function applyMediumBorders(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
    }

    private function formatAddress(mixed $property): string
    {
        $parts = array_filter([
            $property->cim_irsz ?? '',
            $property->cim_varos ?? '',
        ]);

        $streetParts = array_filter([
            $property->cim_utca ?? '',
            $property->cim_utca_addons ?? '',
            $property->cim_hazszam ?? '',
        ]);

        $city = implode(' ', $parts);
        $street = implode(' ', $streetParts);

        if ($city !== '' && $street !== '') {
            return sprintf('%s, %s', $city, $street);
        }

        return $city.$street;
    }

    private function formatRentalFee(mixed $property): string
    {
        $min = $property->min_berleti_dij ?? '';
        $max = $property->max_berleti_dij ?? '';
        $addons = $property->min_berleti_dij_addons ?? '';

        if ($min === '' && $max === '') {
            return '';
        }

        if ($max !== '' && $max !== $min) {
            return mb_trim(sprintf('%s - %s %s', $min, $max, $addons));
        }

        return mb_trim(sprintf('%s %s', $min, $addons));
    }

    private function formatOperatingFee(mixed $property): string
    {
        $fee = $property->uzemeletetesi_dij ?? '';
        $addons = $property->uzemeletetesi_dij_addons ?? '';

        if ($fee === '') {
            return '';
        }

        return mb_trim(sprintf('%s %s', $fee, $addons));
    }

    private function formatCommonAreaRatio(mixed $property): string
    {
        $ratio = $property->kozos_teruleti_arany ?? '';
        $addons = $property->kozos_teruleti_arany_addons ?? '';

        if ($ratio === '') {
            return '';
        }

        return mb_trim(sprintf('%s %s', $ratio, $addons));
    }

    private function formatParkingFee(mixed $property): string
    {
        $min = $property->min_parkolas_dija ?? '';
        $max = $property->max_parkolas_dija ?? '';
        $addons = $property->min_parkolas_dija_addons ?? '';

        if ($min === '' && $max === '') {
            return '';
        }

        if ($max !== '' && $max !== $min) {
            return mb_trim(sprintf('%s - %s %s', $min, $max, $addons));
        }

        return mb_trim(sprintf('%s %s', $min, $addons));
    }

    private function formatMinRentalPeriod(mixed $property): string
    {
        $period = $property->min_berleti_idoszak ?? '';
        $addons = $property->min_berleti_idoszak_addons ?? '';

        if ($period === '') {
            return '';
        }

        return mb_trim(sprintf('%s %s', $period, $addons));
    }
}
