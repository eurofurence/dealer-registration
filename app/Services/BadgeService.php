<?php

namespace App\Services;

use App\Enums\ApplicationType;
use App\Models\Application;
use Com\Tecnick\Pdf\Tcpdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class BadgeService
{
    const PAGE_WIDTH = 85.5;
    const PAGE_HEIGHT = 54;
    const PAGE_DPI = 600;
    private $background_image = null;
    private $pdf = null;

    private $font_sizes = [
        'title_dealer' => 14,
        'title_assistant' => 12,
        'title_goh' => 14,
        'table' => 14,
        'dealership' => 9,
        'dealership_small' => 9,
        'dealership_tiny' => 9,
        'dealership_verytiny' => 7,
        'share' => 6,
        'regid' => 14,
    ];

    public function __construct()
    {
        $pdf = new Tcpdf(
            unit: 'mm',    // Use millimetres as unit,
            isunicode: true,    // Unicode document,
            subsetfont: false,   // Embed full fonts,
            compress: true,    // Use stream compression,
            mode: 'pdfa3', // Conform to PDF/A-3,
            objEncrypt: null,    // Don't use encryption,
        );
        $pdf->setCreator('Eurofurence Dealers\' Den Registration');
        $pdf->setAuthor('Admin');
        $pdf->setSubject('Dealers\' Den Badges');
        $pdf->setTitle('Dealers\' Den Badges');
        $pdf->initClassObjects(fileOptions: [
                'allowedPaths' => array_merge(
                    $pdf->defaultFileAllowedPaths(),
                    [Storage::disk('local')->path('badges')])
            ]);

        if (!defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', Storage::disk('local')->path('badges'));
        }
        if (!Storage::disk('local')->exists('badges/badgefont.json')) {
            new \Com\Tecnick\Pdf\Font\Import(Storage::disk('local')->path('badges/badge-font'));
        }

        // @TODO Make configurable and/or load from storage
        $this->background_image = $pdf->image->add(Storage::disk('local')->path('badges/badge-background'));

        // Load a default font, otherwise the lib barfs when adding a page
        $pdf->font->insert(
            objnum: $pdf->pon,
            font: 'badge-font',
            style: '',
            size: $this->font_sizes['dealership']
        );

        $pdf->setDefaultCellPadding(0, 0, 0, 0);
        $pdf->setDefaultCellMargin(0, 0, 0, 0);
        $this->pdf = $pdf;
    }

    private function useFont(Tcpdf $pdf, string $type) {
        $font = $pdf->font->insert(
            objnum: $pdf->pon,
            font: 'badge-font',
            style: '',
            size: $this->font_sizes[$type]
        );
        $pdf->page->addContent($font['out']);
    }

    public function generateBadge(Application $application, bool $doubleSided = false): void
    {
        $this->addBadgePage($application);
        if ($doubleSided) {
            $this->addBadgePage($application);
        }
    }

    private function addBadgePage(Application $application): void
    {
        $page = $this->pdf->AddPage([
            'width' => $this->getPageWidth(), 
            'height' => $this->getPageHeight()
        ]);

        // Graph needs to have the page size set separately, for some reason...
        $this->pdf->graph->setPageWidth($page['width']);
        $this->pdf->graph->setPageHeight($page['height']);

        $col = $this->pdf->color->getPdfColor('white');
        $this->pdf->page->addContent($col);

        $alignedImage = $this->pdf->image->getSetImage($this->background_image, 0, 0, $this->getPageWidth(), $this->getPageHeight(), $page['height']);
        $this->pdf->page->addContent($alignedImage);

        $this->addBadgeType($this->pdf, $application->type->value);
        if ($application->type == ApplicationType::Share) {
            $this->addShareIndicator($this->pdf);
        }

        if ($application->type === ApplicationType::Dealer) {
            $tableNumber = $application?->table_number;
        } else {
            $tableNumber = $application?->parent?->table_number;
        }

        // Fallback to empty string, this might be more noticeable than just a ??/???
        $this->addTableNumber($this->pdf, $tableNumber ?? "");
        $this->addRegId($this->pdf, $application->user->reg_id ?? "N/A");

        // Default to the application's display name
        $displayName = $application->display_name;

        // If the application type is assistant, grab the parent application for the display name
        if ($application->type == ApplicationType::Assistant) {
            $parentApplication = $application->parent()->first();
            $displayName = $parentApplication->display_name;
        }

        // If the display name is still empty, fallback to the user's name
        if (empty($displayName)) {
            $displayName = $application->user->name;
        }

        $this->addDisplayname($this->pdf, $displayName);
    }

    public function generateCustomBadge(string $type, string $regId, string $displayName, ?string $tableNumber = null, bool $shareIndicator = false, bool $doubleSided = false): void
    {
        $this->addCustomBadgePage(
            $type,
            $regId,
            $displayName,
            $tableNumber,
            $shareIndicator
        );
        if ($doubleSided) {
            $this->addCustomBadgePage(
                $type,
                $regId,
                $displayName,
                $tableNumber,
                $shareIndicator
            );
        }
    }

    private function addCustomBadgePage(string $type, string $regId, string $displayName, ?string $tableNumber = null, bool $shareIndicator = false): void
    {
        $page = $this->pdf->AddPage(['width' => $this->getPageWidth(), 'height' => $this->getPageHeight()]);

        // Graph needs to have the page size set separately, for some reason...
        $this->pdf->graph->setPageWidth($page['width']);
        $this->pdf->graph->setPageHeight($page['height']);

        $col = $this->pdf->color->getPdfColor('white');
        $this->pdf->page->addContent($col);

        $alignedImage = $this->pdf->image->getSetImage($this->background_image, 0, 0, $this->getPageWidth(), $this->getPageHeight(), $page['height']);
        $this->pdf->page->addContent($alignedImage);

        $this->addBadgeType($this->pdf, $type);
        if (!is_null($tableNumber)) {
            $this->addTableNumber($this->pdf, $tableNumber);
        }
        if ($shareIndicator) {
            $this->addShareIndicator($this->pdf);
        }
        $this->addRegId($this->pdf, $regId);
        $this->addDisplayname($this->pdf, $displayName);
    }

    // public function save($name = '', string $prefix = 'DD-Badges_'): string
    public function save($handle): void
    {
        //$filename = 'badges/' . $prefix . $name . '_' . date("Y-m-dTH-i-sO") . '.pdf';
        //Storage::disk('local')->put($filename, $this->pdf->getOutPDFString());
        fwrite($handle, $this->pdf->getOutPDFString());
    }

    public function dumpPdf(): string
    {
        return $this->pdf->getOutPDFString();
    }

    private function addBadgeType(Tcpdf $pdf, string $badgeTypeString): void
    {
        if ($badgeTypeString == ApplicationType::Assistant->value) {
            // Type: Assistant
            $title = 'Assistant';
            $this->useFont($pdf, 'title_assistant');
        } else {
            $title = ucwords($badgeTypeString);
            // Shares are also Dealers
            if ($badgeTypeString == ApplicationType::Share->value) {
                $title = 'Dealer';
            }
            // Guest of Honor
            if ($badgeTypeString == 'goh') {
                $title = 'GoH';
            }
            $this->useFont($pdf, 'title_dealer');
        }
        $badgeType = $this->getTextCell(
            $pdf,
            text: $title,
            pos_x: 1.8,
            pos_y: 36.7,
            width: 27.1,
            height: 8.5,
            params: [
                'halign' => 'C',
                'valign' => 'C',
            ]
        );
        $pdf->page->addContent($badgeType);
    }

    private function addShareIndicator(Tcpdf $pdf): void
    {
        $this->useFont($pdf, 'share');
        $badgeType = $this->getTextCell(
            $pdf,
            text: 'S',
            pos_x: 82.7,
            pos_y: 51.8,
            width: 1.7,
            height: 1.7,
            params: [
                'halign' => 'C',
                'valign' => 'C',
            ]
        );
        $pdf->page->addContent($badgeType);
    }

    private function addTableNumber(Tcpdf $pdf, string $tableNumber): void
    {
        $this->useFont($pdf, 'table');
        $table = $this->getTextCell(
            $pdf,
            text: strtoupper($tableNumber),
            pos_x: 56.4,
            pos_y: 36.7,
            width: 27.1,
            height: 8.5,
            params: [
                'halign' => 'C',
            ]
        );
        $pdf->page->addContent($table);
    }

    private function addRegId(Tcpdf $pdf, string $regIdString): void
    {
        $this->useFont($pdf, 'regid');
        $regId = $this->getTextCell(
            $pdf,
            text: filter_var($regIdString, FILTER_SANITIZE_NUMBER_INT),
            pos_x: 67.2,
            pos_y: 1,
            width: 17.3,
            height: 3.9,
            params: [
                'halign' => 'R',
            ]
        );
        $pdf->page->addContent($regId);
    }

    private function addDisplayname(Tcpdf $pdf, string $displayNameString): void
    {
        $this->useFont($pdf, 'dealership');
        $displayName = $this->getTextCell(
            $pdf,
            text: $displayNameString,
            pos_x: 1.8,
            pos_y: 45.7,
            width: 81.7,
            height: 5.1,
            params: [
                'halign' => 'C',
                'valign' => 'C'
            ]
        );
        $pdf->page->addContent($displayName);
    }

    private function getPageWidth()
    {
        // return $this->pixelsToMm(self::PAGE_WIDTH, self::PAGE_DPI);
        return self::PAGE_WIDTH;
    }

    private function getPageHeight()
    {
        //return $this->pixelsToMm(self::PAGE_HEIGHT, self::PAGE_DPI);
        return self::PAGE_HEIGHT;
    }

    private function getTextCell(
        Tcpdf $pdf,
        string $text,
        float $pos_x = 0,
        float $pos_y = 0,
        float $width = 0,
        float $height = 0,
        array $params = []
    ): string {

        return $pdf->getTextCell(
            txt: $text,
            posx: $pos_x,
            posy: $pos_y,
            width: $width,
            height: $height,
            offset: $params['offset'] ?? 0,
            linespace: $params['linespace'] ??  0,
            valign: $params['valign'] ?? 'C',
            halign: $params['halign'] ?? 'C',
            cell: $params['cell'] ?? null,
            styles: $params['styles'] ?? [
                'all' => [
                    'lineWidth' => 0.1,
                    'lineCap' => 'butt',
                    'lineJoin' => 'miter',
                    'dashArray' => [],
                    'dashPhase' => 0,
                    'lineColor' => 'white',
                    'fillColor' => 'transparent',
                ]
            ],
            strokewidth: $params['strokewidth'] ?? 0,
            wordspacing: $params['wordspacing'] ?? 0,
            leading: $params['leading '] ?? 0,
            rise: $params['rise'] ?? 0,
            jlast: $params['jlast'] ?? true,
            fill: $params['fill'] ?? true,
            stroke: $params['stroke'] ?? false,
            underline: $params['underline'] ?? false,
            linethrough: $params['linethrough'] ?? false,
            overline: $params['overline'] ?? false,
            clip: $params['clip'] ?? false,
            drawcell: $params['drawcell'] ?? false,
            forcedir: $params['forcedir'] ?? '',
            shadow: $params['shadow'] ?? null,
        );
    }
}
