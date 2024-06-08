<?php

namespace App\Console\Commands;

use App\Export\StockCompareExport;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CompareStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compare-stok-from-excel-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchedData = [];
        $mainWarehouseStockData = $this->getMainWarehouseStockData();

        $emreninWarehouse = $this->getEmreninMagazaStockData();

        foreach ($emreninWarehouse as $emreninStock) {
//            if ($emreninStock->stock_code !== '23.01.07.027-C07') {
//                continue;
//            }

            if (isset($searchedData[$emreninStock->stock_code])) {
                continue;
            }

            $searchedData[$emreninStock->stock_code] = true;

            $emreninStockByCode = $emreninWarehouse
                ->where('stock_code', $emreninStock->stock_code);

            $mainStockByCode = $mainWarehouseStockData
                ->where('stock_code', $emreninStock->stock_code);

            $emreninSizes = $emreninStockByCode->pluck('sub_stock_code')->all();

            $mainWarehouseSizes = $mainStockByCode->pluck('sub_stock_code')->all();
            $otherSizes = array_diff($mainWarehouseSizes, $emreninSizes);

            $result[] = [
                'stock_code' => $emreninStock->stock_code,
                'product_name' => $emreninStock->stock_code_description,
                'emrenin-sizes' => implode(',', $emreninSizes),
                'main_sizes' => implode(',', $otherSizes),
            ];
        }

        $export = new StockCompareExport([]);
        Excel::download($export, 'stock_comparison.xlsx');
        return;
    }

    private function getMainWarehouseStockData(): Collection
    {
        return Warehouse::where('stock_location', 'ETİCARET MAĞAZA')
            ->orWhere('stock_location', 'MERKEZ DEPO')
            ->whereIn('category', ['1 - Footwear', '2 - Textile'])
            ->get();
    }

    private function getEmreninMagazaStockData(): Collection
    {
        return Warehouse::where('stock_location', 'HADIMKÖY MAĞAZA')
            ->whereIn('category', ['1 - Footwear', '2 - Textile'])
            ->get();
    }
}
