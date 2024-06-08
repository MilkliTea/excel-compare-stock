<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class StockCompareExport implements FromArray, WithHeadings
{
    public function __construct(protected array $stocks)
    {
    }

    public function array(): array
    {
        return $this->stocks;
    }

    public function headings(): array
    {
        return [
            'Ürün Kodu',
            'Ürün Adı',
            'Hadımköy Bedenler',
            'Merkezdeki Bedenler',
        ];
    }
}
