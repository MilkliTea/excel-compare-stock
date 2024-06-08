<?php

namespace App\Console\Commands;

use App\Epic\ProductPackagePriceCalculation\Models\AllProductPackagePrice;
use App\Import\StockImport;
use App\Import\StokImport;
use App\Imports\PriceJobImport;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;

class InsertStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-stock-from-excel';

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
        $excelData = Excel::toArray(new StockImport(), storage_path('stok.xlsx'));

        $chunkSize = 1000; // Chunks of 1000 rows
        $newRecordCount = 0;
        $existingRecordCount = 0;

        $rows = collect($excelData[0]);

        $rows->chunk($chunkSize)->each(function($chunk) use (&$newRecordCount, &$existingRecordCount) {
            $data = [];

            foreach ($chunk as $row) {
                if ($row['marka_aciklama'] === 'GENEL TOPLAM') {
                    continue;
                }

                $data[] = [
                    'brand' => $row['marka_aciklama'],
                    'stock_code' => $row['stok_kodu'],
                    'stock_code_description' => $row['stok_kodu_aciklama'],
                    'sub_stock_code' => $row['alt_stok_kodu_1'],
                    'category' => $row['anagrup'],
                    'gender' => $row['cinsiyet'],
                    'e_category' => $row['e_kategori'],
                    'stock_location' => $row['stok_yeri_aciklama'],
                    'barcode' => $row['barkod'],
                    'quantity' => $row['miktar']
                ];
            }

            try {
                Warehouse::insert($data);

                $newRecordCount += count($data);
            } catch (QueryException $exception) {
                $errorCode = $exception->errorInfo[1];
                if ($errorCode == 1062) {
                    $existingRecordCount += count($data);
                } else {
                    $this->error($exception->getMessage());
                }
            }
        });

        $this->info('New records: ' . $newRecordCount);
        $this->info('Existing records: ' . $existingRecordCount);
    }
}
