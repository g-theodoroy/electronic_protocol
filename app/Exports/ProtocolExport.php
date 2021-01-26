<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProtocolExport implements FromView, WithEvents
{

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $width = 12;
                $fontsize = 12;
                $maxCol = $event->sheet->getDelegate()->getHighestColumn();
                $maxRow = $event->sheet->getDelegate()->getHighestRow();
                
                $event->sheet->getDelegate()->getStyle('A1:H4')->getFont()->setSize($fontsize)->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:H4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFE0E0E0');
                $event->sheet->getDelegate()->getStyle('A1:H4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFE0E0E0');
                $event->sheet->getDelegate()->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('B2:G2')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A4:H4')->getAlignment()->setHorizontal('center');

                $event->sheet->getDelegate()->getStyle('A1:' . $maxCol . $maxRow)->getFont()->setSize($fontsize);
                $event->sheet->getDelegate()->getStyle('A1:' . $maxCol . $maxRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                $event->sheet->getDelegate()->getStyle('A1:' . $maxCol . $maxRow)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A1:' . $maxCol . $maxRow)->getAlignment()->setVertical('top');

                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth($width);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth($width * 1.75);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth($width * 5);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth($width * 1.25);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth($width * 1.25);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth($width * 3);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth($width * 1.25);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth($width * 4);

                $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(-1);

            },
        ];
    }

    protected $view;
    protected $data;

    public function __construct($view, $data = "")
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function view(): View
    {
        return view(
            $this->view,
            $this->data
        );
    }
}



