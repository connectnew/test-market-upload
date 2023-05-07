<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\BrandRepository;
use PDOException;
use Exception;

class ImportService
{
    public function parse(int $start, int $end, string $folder): array
    {
        $strings = simplexml_load_file($folder . '/xl/sharedStrings.xml');
        $sheet   = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');

        $xlrows = $sheet->sheetData->row;

        $result = [
            'rows' => 0,
            'rowErrors' => [],
        ];

        for ($current = $start; $current <= $end; $current++) {
            if (isset($xlrows[$current])) {

                $row = [];
                $xlrow = $xlrows[$current];
                $rowCount = count($xlrow->c);

                if ($rowCount == 9) {
                    $row[] = "";
                } else if ($rowCount === 8) {
                    $row[] = "";
                    $row[] = "";
                }

                foreach ($xlrow->c as $cell) {

                    $v = (string) $cell->v;
                    if (isset($cell['t']) && $cell['t'] == 's') {
                        $s  = array();
                        $si = $strings->si[(int) $v];

                        $si->registerXPathNamespace('n', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                        foreach($si->xpath('.//n:t') as $t) {
                            $s[] = (string) $t;
                        }

                        $v = implode($s);
                        $row[] = $v;

                    } else {
                        $row[] = $v;
                    }
                }

                /*if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9]+$/', $row[5])) {

                    $resultRow = [
                        'status' => 'error',
                        'number' => $current,
                        'message' => "Error code {$row[5]} is not valid value",
                    ];
                    $result['rowErrors'][] = $resultRow;
                    continue;
                }*/

                if (count($row) === 10) {
                    $resultRow = $this->save($current, $row);
                } else {
                    $resultRow = [
                        'status' => 'error',
                        'number' => $current,
                        'message' => 'Error row columns not equals'
                    ];
                }

                if ($resultRow['status'] === 'ok') {
                    $result['rows'] ++;
                } else {
                    $result['rowErrors'][] = $resultRow;
                }
            }
        }

        return $result;
    }

    protected function save(int $numberRow, array $row): array
    {
        $result = [];
        $product = null;

        DB::beginTransaction();

        try {

            $category = CategoryRepository::saveImport($row);
            $brand = BrandRepository::saveImport($row);
            $product = ProductRepository::saveImport($row, $category, $brand);

            DB::commit();

        } catch (PDOException $e) {

            DB::rollBack();

            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
            $result['number'] = $numberRow;

        } catch (Exception $e) {

            DB::rollBack();

            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
            $result['number'] = $numberRow;
        }

        if ($product) {
            $result['status'] = 'ok';
        }

        return $result;
    }
}
