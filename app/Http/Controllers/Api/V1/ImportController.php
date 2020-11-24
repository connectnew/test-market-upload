<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Traits\ImportTrait;
use App\Http\Requests\Api\V1\ImportController\UploadRequest;
use App\Http\Requests\Api\V1\ImportController\ParseRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Carbon\Carbon;
use PDOException;
use Exception;
use ZipArchive;

class ImportController extends Controller
{
    use ImportTrait;

    public function upload(UploadRequest $request): JsonResponse
    {
        $result = [];

        try {

            $folderName = Carbon::now()->format('Y-m-d_H_i') . '_' . time();
            $fileName = "$folderName.xlsx";

            $request->file('file')->storeAs("public", $fileName);

            $file = Storage::disk('public')->path($fileName);

            Storage::disk('public')->makeDirectory($folderName);

            $folder = storage_path("app/public/$folderName");

            $zip = new ZipArchive();
            $zip->open($file);
            $zip->extractTo($folder);

            $sheet = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');
            $xlrows = $sheet->sheetData->row;

            $result['name'] = $fileName;
            $result['folder'] = $folderName;
            $result['total'] = count($xlrows);

        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'ok', 'file' => $result], 200);
    }

    public function parse(ParseRequest $request): JsonResponse
    {
        $result = [];

        try {

            $folder = storage_path("app/public/{$request->folder}");
            if (is_dir($folder)) {

                $strings = simplexml_load_file($folder . '/xl/sharedStrings.xml');
                $sheet   = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');

                $xlrows = $sheet->sheetData->row;

                $result = $this->chunk($request, $strings, $xlrows);

            } else {
                throw new Exception("Error folder not found");
            }

        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'ok', 'result' => $result], 200);
    }

    protected function chunk(Request $request, &$strings, &$xlrows): array
    {
        $result = [
            'rows' => 0,
            'rowErrors' => [],
        ];

        $start = (int) $request->start;
        $end = (int) $request->end;

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

            $category = $this->saveCategory($row);
            $brand = $this->saveBrand($row);
            $product = $this->saveProduct($row, $category, $brand);

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

    public function test()
    {
        $request = new Request();
        $request->merge([
            'folder' => '2020-11-24_03_08_1606187332',
            'start' => 1,
            'end' => 170,
        ]);

        $folder = storage_path("app/public/{$request->folder}");
        if (is_dir($folder)) {

            $strings = simplexml_load_file($folder . '/xl/sharedStrings.xml');
            $sheet   = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');

            $xlrows = $sheet->sheetData->row;

            $result = $this->chunk($request, $strings, $xlrows);

            dd($result);

        } else {
            throw new Exception("Error folder not found");
        }
    }
}

