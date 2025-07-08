<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Pages;
use Fpdf\Fpdf;

class CoordinateController extends Controller
{
    public function getCoordinate(Request $request) {
        $stampMarginVertical = 10; //untuk mengatur margin vertical dari ematerai
        $stampMarginHorizontal = -100; //untuk mengatur margin horizontal dari ematerai
        $stampSize = 60; //untuk mengatur ukuran ematerai
        $stampFontSize = 30; //font size dari textUnderEmateraiPosition bisa disesuaikan 
        $textUnderEmateraiPosition = "Angi Tania"; //bisa di liat di dalam pdfnya ada kata" cobaaaa untuk acuan meletakan ematerai diatas kata cobaaaa
        // $textUnderEmateraiPosition = "cobaaaa"; //bisa di liat di dalam pdfnya ada kata" cobaaaa untuk acuan meletakan ematerai diatas kata cobaaaa
        // $pdfURL = "https://storage.googleapis.com/ppr-stg/export/b74d8c23-e3ec-469f-8efa-e61010282163/sales-invoice/pdf/INV_SAL_INV_API_2022_100001436.pdf";
        $pdfURL = "http://cdn.ifca.co.id/UNSIGNED/CKFJWG/IU23070116.pdf";
        
        $el = $textUnderEmateraiPosition;
        if ($el === '' || $el === null) {
            $el = ' ';
        }

        $parts = preg_split("/\r\n|\n|\r/", $el);
        $el = $parts[0];

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfURL);
        $pages = $pdf->getPages();
        
        $elements = [];
        for($i = 0 ; $i < count($pages); $i++) {
            $page = $pages[$i];
            $element = $this->getDataTm($page);

            $elements[$i+1]= $element;
        }

        $metadata = [];

        $validPages = 0;
        foreach ($elements as $key => $value) {
            for ($i = count($value) - 1; $i >= 0; $i--) {
                $val = $value[$i];
                if (trim($val[1]) == $el) {
                    $fpdf = new Fpdf();
                    $fpdf->SetFont('Arial', 'B', $stampFontSize);
                    $width = $fpdf->GetStringWidth($val[1]);

                    $metadata['stamps'] = [
                        'string' => $val[1],
                        'width' => $width,
                        'coordinates' => [
                            'x' => (float)$val[0][4],
                            'y' => (float)$val[0][5],
                        ],
                    ];

                    $validPages = $key;
                    break;
                }
            }
        }

        $metadata['pages'] = $validPages;
        $x = $metadata['stamps']['coordinates']['x'] + $metadata['stamps']['width'] / 2;

        $metadata['position'] = [
            'llx' => $x - ($stampSize / 2),
            'lly' => $metadata['stamps']['coordinates']['y'] + $stampMarginVertical,
            'urx' => $x + ($stampSize / 2),
            'ury' => $metadata['stamps']['coordinates']['y'] + $stampMarginVertical + $stampSize,
        ];

        dd($metadata);
    }

    protected function getDataTm(Page $page)
    {
        $dataCommands = $page->getDataCommands();

        $defaultTm = ['1', '0', '0', '1', '0', '0'];
        $defaultTl = 0;
        $x = 4;
        $y = 5;
        $Tx = 0;
        $Ty = 0;
        $Tm = $defaultTm;
        $Tl = $defaultTl;

        $extractedTexts = $page->getTextArray();
        $extractedData = [];
        foreach ($dataCommands as $command) {
            if (!isset($extractedTexts[count($extractedData)])) {
                continue;
            }
            $currentText = $extractedTexts[count($extractedData)];

            switch ($command['o']) {
                case 'BT':
                    $Tm = $defaultTm;
                    $Tl = $defaultTl;
                    $Tx = 0;
                    $Ty = 0;

                    break;

                case 'ET':
                    $Tm = $defaultTm;
                    $Tl = $defaultTl;
                    $Tx = 0;
                    $Ty = 0;

                    break;

                case 'TL':
                    $Tl = (float)$command['c'];

                    break;

                case 'Td':
                    $coord = explode(' ', $command['c']);
                    $Tx += (float)$coord[0];
                    $Ty += (float)$coord[1];
                    $Tm[$x] = (string)$Tx;
                    $Tm[$y] = (string)$Ty;

                    break;

                case 'TD':
                    $coord = explode(' ', $command['c']);
                    $Tl = (float)$coord[1];
                    $Tx += (float)$coord[0];
                    $Ty -= (float)$coord[1];
                    $Tm[$x] = (string)$Tx;
                    $Tm[$y] = (string)$Ty;

                    break;

                case 'Tm':
                    $Tm = explode(' ', $command['c']);
                    $Tx = (float)$Tm[$x];
                    $Ty = (float)$Tm[$y];

                    break;

                case 'T*':
                    $Ty -= $Tl;
                    $Tm[$y] = (string)$Ty;

                    break;

                case 'Tj':
                    $extractedData[] = [$Tm, $currentText];

                    break;

                case "'":
                    $Ty -= $Tl;
                    $Tm[$y] = (string)$Ty;
                    $extractedData[] = [$Tm, $currentText];

                    break;

                case '"':
                    $data = explode(' ', $currentText);
                    $Ty -= $Tl;
                    $Tm[$y] = (string)$Ty;
                    $extractedData[] = [$Tm, $data[2]];

                    break;

                case 'TJ':
                    $extractedData[] = [$Tm, $currentText];

                    break;

                default:
            }
        }

        return $extractedData;
    }
}
