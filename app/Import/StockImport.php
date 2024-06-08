<?php

namespace App\Import;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
    }
}
