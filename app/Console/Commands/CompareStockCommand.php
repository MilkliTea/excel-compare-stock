<?php

namespace App\Console\Commands;

use App\Enums\Store;
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
    protected $description = 'Mağazaları karşılaştırır ve ürün Bedenlerinin farklarını excel dosyasına yazar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchedData = [];
        $mainWarehouseStockData = $this->getMainWarehouseStockData();

        $storeWarehouseData = $this->getStoreWarehouseStockData();

        foreach ($storeWarehouseData as $storeStock) {
            $productCode = $storeStock->stock_code;
            $productName = $storeStock->stock_code_description;

            if (isset($searchedData[$productCode])) {
                continue;
            }

            $searchedData[$productCode] = true;

            $result[] = $this->compareStockData($productCode, $productName, $mainWarehouseStockData, $storeWarehouseData);
        }

        $excel = new StockCompareExport($result);

        Excel::store($excel, 'stock_compasdaasdrison.xlsx', 'public');
    }

    private function getMainWarehouseStockData(): Collection
    {
        return Warehouse::whereIn('stock_location', Store::mainStores())
            ->whereIn('category', ['1 - Footwear', '2 - Textile'])
            ->get();
    }

    private function getStoreWarehouseStockData(): Collection
    {
        return Warehouse::where('stock_location', Store::HADIMKOY->value)
            ->whereIn('category', ['1 - Footwear', '2 - Textile'])
            ->get();
    }

    private function compareStockData(string $productCode, string $productName, Collection $mainWarehouseStockData, Collection $storeWarehouseData): array
    {
        $storeStockByCode = $storeWarehouseData
            ->where('stock_code', $productCode);

        $mainStockByCode = $mainWarehouseStockData
            ->where('stock_code', $productCode);

        $storeSizes = $storeStockByCode->pluck('sub_stock_code')->all();

        $mainWarehouseSizes = $mainStockByCode->pluck('sub_stock_code')->all();

        $otherSizes = array_diff($mainWarehouseSizes, $storeSizes);

        return [
            'stock_code' => $productCode,
            'product_name' => $productName,
            'store_sizes' => implode(', ', $storeSizes),
            'main_warehouse_sizes' => implode(', ', $otherSizes),
        ];
    }
}
