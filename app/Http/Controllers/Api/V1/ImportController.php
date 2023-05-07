<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ImportController\UploadRequest;
use App\Http\Requests\Api\V1\ImportController\ParseRequest;
use App\Http\Actions\Api\V1\Import\UploadAction;
use Illuminate\Http\JsonResponse;
use App\Services\ImportService;
use Exception;
use ZipArchive;

class ImportController extends Controller
{
    public function upload(UploadRequest $request): JsonResponse
    {
        $result = [];

        try {

            $action = new UploadAction();
            $result = $action->execute($request);

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

                $result = $service->parse((int) $request->start, (int) $request->end, $folder);

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
        $file = '2020-11-24_03_08_1606187332';
        $start = 1;
        $end = 170;

        $service = new ImportService();

        $folder = storage_path("app/public/{$file}");
        if (is_dir($folder)) {

            $result = $service->parse($start, $end, $folder);

            dd($result);

        } else {
            throw new Exception("Error folder not found");
        }
    }
}

