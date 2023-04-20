<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ImportController\UploadRequest;
use App\Http\Requests\Api\V1\ImportController\ParseRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use ZipArchive;

class ImportController extends Controller
{
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
        $service = new ImportService();

        try {

            $folder = storage_path("app/public/{$request->folder}");
            if (is_dir($folder)) {

                $strings = simplexml_load_file($folder . '/xl/sharedStrings.xml');
                $sheet   = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');

                $xlrows = $sheet->sheetData->row;

                $result = $service->chunk($request, $strings, $xlrows);

            } else {
                throw new Exception("Error folder not found");
            }

        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'ok', 'result' => $result], 200);
    }

    public function test()
    {
        $request = new Request();
        $request->merge([
            'folder' => '2020-11-24_03_08_1606187332',
            'start' => 1,
            'end' => 170,
        ]);

        $service = new ImportService();

        $folder = storage_path("app/public/{$request->folder}");
        if (is_dir($folder)) {

            $strings = simplexml_load_file($folder . '/xl/sharedStrings.xml');
            $sheet   = simplexml_load_file($folder . '/xl/worksheets/sheet1.xml');

            $xlrows = $sheet->sheetData->row;

            $result = $service->chunk($request, $strings, $xlrows);

            dd($result);

        } else {
            throw new Exception("Error folder not found");
        }
    }
}

