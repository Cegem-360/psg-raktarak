<?php

declare(strict_types=1);

use App\Services\FavoritesExcelExportService;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

beforeEach(function (): void {
    $this->service = new FavoritesExcelExportService;
});

afterEach(function (): void {
    if (property_exists($this, 'tempFile') && $this->tempFile !== null && file_exists($this->tempFile)) {
        File::delete($this->tempFile);
    }
});

function createPropertyStub(array $overrides = []): stdClass
{
    $defaults = [
        'title' => 'Test Office Building',
        'postal_code' => '1051',
        'city' => 'Budapest',
        'street' => 'Nádor',
        'street_type' => 'utca',
        'house_number' => '23',
        'construction_year' => '2010',
        'min_rent' => '12',
        'max_rent' => '18',
        'min_rent_unit' => 'EUR/m2/hó',
        'operating_fee' => '4.5',
        'operating_fee_unit' => 'EUR/m2/hó',
        'common_area_ratio' => '5%',
        'common_area_ratio_unit' => '',
        'min_parking_fee' => '120',
        'max_parking_fee' => '150',
        'min_parking_fee_unit' => 'EUR/hely/hó',
        'max_parking_fee_unit' => 'EUR/hely/hó',
        'min_lease_term' => '3',
        'min_lease_term_unit' => 'év',
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
        'postal_code' => '1051',
        'city' => 'Budapest',
        'street' => 'Nádor',
        'street_type' => 'utca',
        'house_number' => '23',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('B4')->getValue())->toBe('1051 Budapest, Nádor utca 23');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee with range when min and max differ', function (): void {
    $properties = collect([createPropertyStub([
        'min_rent' => '12',
        'max_rent' => '18',
        'min_rent_unit' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('12 - 18 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee without range when min equals max', function (): void {
    $properties = collect([createPropertyStub([
        'min_rent' => '15',
        'max_rent' => '15',
        'min_rent_unit' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('15 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats rental fee without range when max is empty', function (): void {
    $properties = collect([createPropertyStub([
        'min_rent' => '15',
        'max_rent' => '',
        'min_rent_unit' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('D4')->getValue())->toBe('15 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats parking fee with range', function (): void {
    $properties = collect([createPropertyStub([
        'min_parking_fee' => '120',
        'max_parking_fee' => '150',
        'min_parking_fee_unit' => 'EUR/hely/hó',
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
        'postal_code' => null,
        'city' => null,
        'street' => null,
        'street_type' => null,
        'house_number' => null,
        'construction_year' => null,
        'min_rent' => null,
        'max_rent' => null,
        'min_rent_unit' => null,
        'operating_fee' => null,
        'operating_fee_unit' => null,
        'common_area_ratio' => null,
        'common_area_ratio_unit' => null,
        'min_parking_fee' => null,
        'max_parking_fee' => null,
        'min_parking_fee_unit' => null,
        'min_lease_term' => null,
        'min_lease_term_unit' => null,
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
        'operating_fee' => '4.5',
        'operating_fee_unit' => 'EUR/m2/hó',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('E4')->getValue())->toBe('4.5 EUR/m2/hó');

    $spreadsheet->disconnectWorksheets();
});

it('formats common area ratio correctly', function (): void {
    $properties = collect([createPropertyStub([
        'common_area_ratio' => '5%',
        'common_area_ratio_unit' => '',
    ])]);

    $this->tempFile = $this->service->generate($properties);
    $spreadsheet = IOFactory::load($this->tempFile);
    $sheet = $spreadsheet->getActiveSheet();

    expect($sheet->getCell('F4')->getValue())->toBe('5%');

    $spreadsheet->disconnectWorksheets();
});

it('formats min rental period correctly', function (): void {
    $properties = collect([createPropertyStub([
        'min_lease_term' => '3',
        'min_lease_term_unit' => 'év',
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
