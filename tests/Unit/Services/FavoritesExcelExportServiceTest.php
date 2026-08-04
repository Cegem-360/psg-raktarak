<?php

declare(strict_types=1);

use App\Services\FavoritesExcelExportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

beforeEach(function (): void {
    $this->service = new FavoritesExcelExportService;
});

afterEach(function (): void {
    if (isset($this->tempFile) && file_exists($this->tempFile)) {
        @unlink($this->tempFile);
    }
});

function createPropertyStub(array $overrides = []): stdClass
{
    $defaults = [
        'title' => 'Test Office Building',
        'cim_irsz' => '1051',
        'cim_varos' => 'Budapest',
        'cim_utca' => 'Nádor',
        'cim_utca_addons' => 'utca',
        'cim_hazszam' => '23',
        'construction_year' => '2010',
        'min_berleti_dij' => '12',
        'max_berleti_dij' => '18',
        'min_berleti_dij_addons' => 'EUR/m2/hó',
        'uzemeletetesi_dij' => '4.5',
        'uzemeletetesi_dij_addons' => 'EUR/m2/hó',
        'kozos_teruleti_arany' => '5%',
        'kozos_teruleti_arany_addons' => '',
        'min_parkolas_dija' => '120',
        'max_parkolas_dija' => '150',
        'min_parkolas_dija_addons' => 'EUR/hely/hó',
        'max_parkolas_dija_addons' => 'EUR/hely/hó',
        'min_berleti_idoszak' => '3',
        'min_berleti_idoszak_addons' => 'év',
    ];

    return (object) array_merge($defaults, $overrides);
}

it('generates a valid xlsx file', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);

    expect($this->tempFile)->toBeString()
        ->and(file_exists($this->tempFile))->toBeTrue();

    $spreadsheet = IOFactory::load($this->tempFile);
    expect($spreadsheet->getSheetCount())->toBe(1);

    $spreadsheet->disconnectWorksheets();
});

it('names the worksheet munkafuzet', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getTitle())->toBe('munkafüzet');

    $spreadsheet->disconnectWorksheets();
});

it('sets the title row correctly', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('B1')->getValue())->toBe('PSG-RAKTARAK.HU');

    // Check merge range
    $mergedCells = $sheet->getMergeCells();
    expect($mergedCells)->toHaveKey('B1:H1');

    // Check bold
    expect($sheet->getStyle('B1')->getFont()->getBold())->toBeTrue();

    $spreadsheet->disconnectWorksheets();
});

it('sets all header labels in row 3', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('A3')->getValue())->toBe('Raktár neve')
        ->and($sheet->getCell('B3')->getValue())->toBe('Raktár címe')
        ->and($sheet->getCell('C3')->getValue())->toBe('Építés éve')
        ->and($sheet->getCell('D3')->getValue())->toBe('Bérleti díj')
        ->and($sheet->getCell('E3')->getValue())->toBe('Üzemeltetési díj')
        ->and($sheet->getCell('F3')->getValue())->toBe('Közös területi arány')
        ->and($sheet->getCell('G3')->getValue())->toBe('Parkolóhely díja')
        ->and($sheet->getCell('H3')->getValue())->toBe('Min. bérleti időszak')
        ->and($sheet->getCell('I3')->getValue())->toBe('Ajánlott terület');

    // Check headers are bold
    expect($sheet->getStyle('A3')->getFont()->getBold())->toBeTrue();
    expect($sheet->getStyle('I3')->getFont()->getBold())->toBeTrue();

    $spreadsheet->disconnectWorksheets();
});

it('populates data rows starting at row 4', function (): void {
    $properties = collect([
        createPropertyStub(['title' => 'Office Alpha']),
        createPropertyStub(['title' => 'Office Beta']),
    ]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('A4')->getValue())->toBe('Office Alpha')
        ->and($sheet->getCell('A5')->getValue())->toBe('Office Beta');

    $spreadsheet->disconnectWorksheets();
});

it('formats address correctly', function (): void {
    $properties = collect([createPropertyStub([
        'cim_irsz' => '1051',
        'cim_varos' => 'Budapest',
        'cim_utca' => 'Nádor',
        'cim_utca_addons' => 'utca',
        'cim_hazszam' => '23',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('B4')->getValue())->toBe('1051 Budapest, Nádor utca 23');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee with range when min and max differ', function (): void {
    $properties = collect([createPropertyStub([
        'min_berleti_dij' => '12',
        'max_berleti_dij' => '18',
        'min_berleti_dij_addons' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('12 - 18 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee without range when min equals max', function (): void {
    $properties = collect([createPropertyStub([
        'min_berleti_dij' => '15',
        'max_berleti_dij' => '15',
        'min_berleti_dij_addons' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('15 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee without range when max is empty', function (): void {
    $properties = collect([createPropertyStub([
        'min_berleti_dij' => '15',
        'max_berleti_dij' => '',
        'min_berleti_dij_addons' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('15 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats parking fee with range', function (): void {
    $properties = collect([createPropertyStub([
        'min_parkolas_dija' => '120',
        'max_parkolas_dija' => '150',
        'min_parkolas_dija_addons' => 'EUR/hely/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('G4')->getValue())->toBe('120 - 150 EUR/hely/hó');

    $spreadsheet->disconnectWorksheets();
});

it('leaves column I empty for user input', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('I4')->getValue())->toBeNull();

    $spreadsheet->disconnectWorksheets();
});

it('applies medium borders to header and data rows', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    // Check header borders
    $headerBorder = $sheet->getStyle('A3')->getBorders()->getTop()->getBorderStyle();
    expect($headerBorder)->toBe(Border::BORDER_MEDIUM);

    // Check data row borders
    $dataBorder = $sheet->getStyle('A4')->getBorders()->getTop()->getBorderStyle();
    expect($dataBorder)->toBe(Border::BORDER_MEDIUM);

    $spreadsheet->disconnectWorksheets();
});

it('sets correct column widths', function (): void {
    $properties = collect([createPropertyStub()]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getColumnDimension('A')->getWidth())->toBe(16.0)
        ->and($sheet->getColumnDimension('B')->getWidth())->toBe(19.11)
        ->and($sheet->getColumnDimension('I')->getWidth())->toBe(15.22);

    $spreadsheet->disconnectWorksheets();
});

it('handles empty collection gracefully', function (): void {
    $properties = collect();

    $this->tempFile = $this->service->generate($properties);

    expect(file_exists($this->tempFile))->toBeTrue();

    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    // Should still have headers but no data
    expect($sheet->getCell('A3')->getValue())->toBe('Raktár neve')
        ->and($sheet->getCell('A4')->getValue())->toBeNull();

    $spreadsheet->disconnectWorksheets();
});

it('handles properties with null fields', function (): void {
    $properties = collect([createPropertyStub([
        'title' => null,
        'cim_irsz' => null,
        'cim_varos' => null,
        'cim_utca' => null,
        'cim_utca_addons' => null,
        'cim_hazszam' => null,
        'construction_year' => null,
        'min_berleti_dij' => null,
        'max_berleti_dij' => null,
        'min_berleti_dij_addons' => null,
        'uzemeletetesi_dij' => null,
        'uzemeletetesi_dij_addons' => null,
        'kozos_teruleti_arany' => null,
        'kozos_teruleti_arany_addons' => null,
        'min_parkolas_dija' => null,
        'max_parkolas_dija' => null,
        'min_parkolas_dija_addons' => null,
        'min_berleti_idoszak' => null,
        'min_berleti_idoszak_addons' => null,
    ])]);

    $this->tempFile = $this->service->generate($properties);

    expect(file_exists($this->tempFile))->toBeTrue();

    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    // Should not throw, cells should be empty or null
    expect($sheet->getCell('A4')->getValue())->toBeIn(['', null])
        ->and($sheet->getCell('D4')->getValue())->toBeIn(['', null])
        ->and($sheet->getCell('E4')->getValue())->toBeIn(['', null])
        ->and($sheet->getCell('G4')->getValue())->toBeIn(['', null]);

    $spreadsheet->disconnectWorksheets();
});

it('formats operating fee correctly', function (): void {
    $properties = collect([createPropertyStub([
        'uzemeletetesi_dij' => '4.5',
        'uzemeletetesi_dij_addons' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('E4')->getValue())->toBe('4.5 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats common area ratio correctly', function (): void {
    $properties = collect([createPropertyStub([
        'kozos_teruleti_arany' => '5%',
        'kozos_teruleti_arany_addons' => '',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('F4')->getValue())->toBe('5%');

    $spreadsheet->disconnectWorksheets();
});

it('formats min rental period correctly', function (): void {
    $properties = collect([createPropertyStub([
        'min_berleti_idoszak' => '3',
        'min_berleti_idoszak_addons' => 'év',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('H4')->getValue())->toBe('3 év');

    $spreadsheet->disconnectWorksheets();
});

it('populates construction year from property', function (): void {
    $properties = collect([createPropertyStub([
        'construction_year' => '2015',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    // PhpSpreadsheet may convert numeric strings to integers
    expect((string) $sheet->getCell('C4')->getValue())->toBe('2015');

    $spreadsheet->disconnectWorksheets();
});

it('handles multiple properties with correct row placement', function (): void {
    $properties = collect([
        createPropertyStub(['title' => 'First Building', 'construction_year' => '2010']),
        createPropertyStub(['title' => 'Second Building', 'construction_year' => '2015']),
        createPropertyStub(['title' => 'Third Building', 'construction_year' => '2020']),
    ]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('A4')->getValue())->toBe('First Building')
        ->and((string) $sheet->getCell('C4')->getValue())->toBe('2010')
        ->and($sheet->getCell('A5')->getValue())->toBe('Second Building')
        ->and((string) $sheet->getCell('C5')->getValue())->toBe('2015')
        ->and($sheet->getCell('A6')->getValue())->toBe('Third Building')
        ->and((string) $sheet->getCell('C6')->getValue())->toBe('2020');

    $spreadsheet->disconnectWorksheets();
});
