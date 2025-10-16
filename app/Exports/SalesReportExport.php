<?php

namespace App\Exports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\PriceHelper;


class SalesReportExport implements FromQuery, WithHeadings, WithStyles
{
    use Exportable;

    protected $orders;
    protected $totals;
    protected $filters;
    protected $settings;

    public function __construct($orders, $totals, $filters)
    {
        $this->orders = $orders;
        $this->totals = $totals;
        $this->filters = $filters;
        $this->settings = Setting::first();
    }

    public function query()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        $filters = '';
        foreach ($this->filters as $key => $filter){
            $filters .= $key .' : '. $filter. ',';
        }
        $overallFilters = __('Overall Report').' - '.$filters;
        return [
            [
                Setting::first()->app_name
            ],
            [],
            [
                $overallFilters
            ],
            [],
            [
                __('Summary')
            ],
            [],
            [
                __('Total sale amount'),
                __('Total cost amount'),
                __('Total discount amount'),
                __('Total profit amount'),
                __('Total tax amount'),
                __('Total payable amount')
            ],
            [
                PriceHelper::formatPrice($this->totals['total_cart_price'], $this->settings),
                PriceHelper::formatPrice($this->totals['total_cart_cost'], $this->settings),
                PriceHelper::formatPrice($this->totals['total_discount'], $this->settings),
                PriceHelper::formatPrice($this->totals['total_profit'], $this->settings),
                PriceHelper::formatPrice($this->totals['total_tax'], $this->settings),
                PriceHelper::formatPrice($this->totals['total_payable'], $this->settings)
            ],
            [],
            [
                __('Detailed report')
            ],
            [],
            [
                __('Receipt #'),
                __('Order'),
                __('Order type'),
                __('POS / eKiosk'),
                __('Paid with'),
                __('Cost'),
                __('Discount'),
                __('Profit'),
                __('Tax amount'),
                __('Payable'),
                __('Date created'),
                __('Date updated'),
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $this->orders->count() + 1;

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 15
            ],
        ]);

        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 15
            ],
        ]);

        $sheet->getStyle('A5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 15
            ],
        ]);

        $sheet->getStyle('A10')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 15
            ],
        ]);
        $sheet->getStyle('A12:L' . $highestRow+11)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'wrapText' => true,
            ],
        ]);
        $sheet->getStyle('A7:F8')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'wrapText' => true,
            ],
            'font' => [
                'bold' => true,
                'size' => 13
            ],
        ]);
        $sheet->getStyle('A12:L12')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC0C0C0'],
            ],
            'font' => [
                'bold' => true,
                'size' => 13
            ],
        ]);
        $sheet->getStyle('A7:F7')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC0C0C0'],
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(50);
        $sheet->getRowDimension(8)->setRowHeight(40);
        $sheet->getRowDimension(12)->setRowHeight(47);
        $sheet->getRowDimension(4)->setRowHeight(35);
        $sheet->getRowDimension(9)->setRowHeight(35);
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setWidth(15);
        }
    }

}
