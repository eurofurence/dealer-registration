<?php

namespace App\Services;

use App\Enums\ApplicationType;
use App\Models\Application;
use Com\Tecnick\Pdf\Tcpdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class BadgeService
{
    const PAGE_WIDTH = 2020;
    const PAGE_HEIGHT = 1296;
    const PAGE_DPI = 600;
    private $title_dealer_font = null;
    private $title_assistant_font = null;
    private $title_goh_font = null;
    private $table_font = null;
    private $dealership_font = null;
    private $dealership_font_small = null;
    private $dealership_font_tiny = null;
    private $dealership_font_verytiny = null;
    private $share_font = null;
    private $reg_font = null;
    private $background_image = null;
    private $pdf = null;

    public function __construct()
    {
        $pdf = new Tcpdf(
            'mm',    // Use millimetres as unit,
            true,    // Unicode document,
            false,   // Embed full fonts,
            true,    // Use stream compression,
            'pdfa3', // Conform to PDF/A-1,
            null,    // Don't use encryption,
        );
        $pdf->setCreator('Eurofurence Dealers\' Den Registration');
        $pdf->setAuthor('Admin');
        $pdf->setSubject('Dealers\' Den Badges');
        $pdf->setTitle('Dealers\' Den Badges');

        if (!defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', Storage::disk('local')->path('badges/'));
        }
        if (!Storage::disk('local')->exists('badges/badgefont.json')) {
            new \Com\Tecnick\Pdf\Font\Import(Storage::disk('local')->path('badges/badge-font'));
        }

        $this->title_dealer_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 18);
        $this->title_assistant_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 13);
        $this->title_goh_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 14);

        $this->table_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 18);

        $this->dealership_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 11);
        $this->dealership_font_small = $pdf->font->insert($pdf->pon, 'badge-font', '', 9);
        $this->dealership_font_tiny = $pdf->font->insert($pdf->pon, 'badge-font', '', 7);
        $this->dealership_font_verytiny = $pdf->font->insert($pdf->pon, 'badge-font', '', 5);
        $this->share_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 10);

        $this->reg_font = $pdf->font->insert($pdf->pon, 'badge-font', '', 18);

        // @TODO Make configurable and/or load from storage
        $this->background_image = $pdf->image->add(Storage::disk('local')->path('badges/badge-background'));

        $pdf->setDefaultCellPadding(0, 0, 0, 0);
        $pdf->setDefaultCellMargin(0, 0, 0, 0);
        $this->pdf = $pdf;
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
        $page = $this->pdf->AddPage(['width' => $this->getPageWidth(), 'height' => $this->getPageHeight()]);

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
        // @TODO tableNumber returns null
        $this->addTableNumber($this->pdf, $application->tableNumber ?? "");
        $this->addRegId($this->pdf, $application->user->reg_id ?? "N/A");
        // KLUDGE: display_name can be empty and depends on the type
        $displayName = $application->user->name;
        if ($application->type == ApplicationType::Assistant) {
            // Get the parent application and use it's display name
            $parentApplication = $application->parent()->first();
            $displayName = $parentApplication->user->name;
            if (!empty($displayName)) {
                $displayName = $parentApplication->display_name;
            }
        } else {
            if (!empty($application->display_name)) {
                $displayName = $application->display_name;
            }
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
            $pdf->page->addContent($this->title_assistant_font['out']);
            $badgeType = $this->getTextCell(
                $pdf,
                "Assistant",
                $this->pixelsToMm(1290),
                $this->pixelsToMm(447),
                $this->pixelsToMm(790),
                $this->pixelsToMm(259),
                [
                    'valign' => 'C',
                    'halign' => 'C',
                ]
            );
            $pdf->page->addContent($badgeType);
        } else {
            $title = ucwords($badgeTypeString);
            if ($badgeTypeString == ApplicationType::Share->value) {
                $title = 'Dealer';
            }
            if ($badgeTypeString == 'goh') {
                $title = 'GoH';
                $pdf->page->addContent($this->title_dealer_font['out']);
            } else {
                $pdf->page->addContent($this->title_dealer_font['out']);
            }
            $badgeType = $this->getTextCell(
                $pdf,
                $title,
                $this->pixelsToMm(1166 - 20),
                $this->pixelsToMm(457 + 18),
                $this->pixelsToMm(790),
                $this->pixelsToMm(259),
                [
                    'halign' => 'C',
                ]
            );
            $pdf->page->addContent($badgeType);
        }
    }

    private function addShareIndicator(Tcpdf $pdf): void
    {
        $pdf->page->addContent($this->share_font['out']);
        $badgeType = $this->getTextCell(
            $pdf,
            'S',
            $this->pixelsToMm(1980 - 28),
            $this->pixelsToMm(1251 - 72),
            $this->pixelsToMm(80),
            $this->pixelsToMm(80),
            [
                'halign' => 'C',
                'valign' => 'C',
            ]
        );
        $pdf->page->addContent($badgeType);
    }

    private function addTableNumber(Tcpdf $pdf, string $tableNumber): void
    {
        $pdf->page->addContent($this->table_font['out']);
        $table = $this->getTextCell(
            $pdf,
            strtoupper($tableNumber),
            $this->pixelsToMm(1166 - 20),
            $this->pixelsToMm(763 + 16),
            $this->pixelsToMm(790),
            $this->pixelsToMm(259),
            [
                'halign' => 'C',
            ]
        );
        $pdf->page->addContent($table);
    }

    private function addRegId(Tcpdf $pdf, string $regIdString): void
    {
        $pdf->page->addContent($this->reg_font['out']);
        $regId = $this->getTextCell(
            $pdf,
            filter_var($regIdString, FILTER_SANITIZE_NUMBER_INT),
            $this->pixelsToMm(1980 - 520),
            $this->pixelsToMm(25 + 16),
            $this->pixelsToMm(500),
            $this->pixelsToMm(130),
            [
                'halign' => 'R',
            ]
        );
        $pdf->page->addContent($regId);
    }

    private function addDisplayname(Tcpdf $pdf, string $displayNameString): void
    {
        $displayNameLength = strlen($displayNameString);
        $factor = 1.0;
        if ($displayNameLength <= 26) {
            // Foo Bar Baz Fiz Fuz Quc Fa
            $pdf->page->addContent($this->dealership_font['out']);
            $factor = 0.63;
        } elseif ($displayNameLength <= 36) {
            // Foo Bar Baz Fiz Fuz Quc Far Fii Foo
            $pdf->page->addContent($this->dealership_font_small['out']);
            $factor = 0.5;
        } else {
            // Foo Bar Baz Fiz Fuz Quc Far Foo Fii F
            $pdf->page->addContent($this->dealership_font_tiny['out']);
            $factor = 0.4;
        }

        // Dummy text line so the BB works
        $displayName = $pdf->getTextLine($displayNameString, 0, 10);
        $pdf->page->addContent($displayName);
        $bb = $pdf->getLastBBox();

        // This factor is a kludge...
        // TCPDF has some weird and fucky way of calculating the bounding box
        // Which does not work...
        $width = $this->mmToPixels($bb['w'] * $factor);
        $posX = floor(100 + ((1840 - $width) / 2));
        $pdf->page->popContent();

        $displayName = $pdf->getTextLine(
            $displayNameString,
            $this->pixelsToMm($posX),
            $this->pixelsToMm(1160 + 12),
        );
        $pdf->page->addContent($displayName);
    }

    private function getPageWidth()
    {
        return $this->pixelsToMm(self::PAGE_WIDTH, self::PAGE_DPI);
    }

    private function getPageHeight()
    {
        return $this->pixelsToMm(self::PAGE_HEIGHT, self::PAGE_DPI);
    }

    private function pixelsToMm(int $dimension, int $dpi = 600): float
    {
        return $dimension / ($dpi / 25.4);
    }

    private function mmToPixels(int $dimension, int $dpi = 600): float
    {
        return $dimension * ($dpi / 25.4);
    }
    private function pixelsToPt(int $dimension): float
    {
        return $dimension * (72 / 96);
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
            $text,
            $pos_x,
            $pos_y,
            $width,
            $height,
            $params['offset'] ?? 0,
            $params['linespace'] ??  0,
            $params['valign'] ?? 'C',
            $params['halign'] ?? 'C',
            $params['cell'] ?? null,
            $params['styles'] ?? [
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
            $params['strokewidth'] ?? 0,
            $params['wordspacing'] ?? 0,
            $params['leading '] ?? 0,
            $params['rise'] ?? 0,
            $params['jlast'] ?? true,
            $params['fill'] ?? true,
            $params['stroke'] ?? true,
            $params['underline'] ?? false,
            $params['linethrough'] ?? false,
            $params['overline'] ?? false,
            $params['clip'] ?? false,
            $params['drawcell'] ?? false,
            $params['forcedir'] ?? '',
            $params['shadow'] ?? null
        );
    }
}
