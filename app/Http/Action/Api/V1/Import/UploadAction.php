<?php

namespace App\Http\Actions\Api\V1\Import;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use ZipArchive;

class UploadAction {

    public function execute(Request $request): array
    {
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

        return $result;
    }
}
