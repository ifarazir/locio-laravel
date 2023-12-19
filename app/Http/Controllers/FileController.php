<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Admin\FileResponse;
use App\ProtectionLayers\EnsureFileIdExists;
use App\Services\File\FileService;
use App\Services\Uploader\Uploader;
use Illuminate\Http\Request;
use Imanghafoori\HeyMan\Facades\HeyMan;
use Imanghafoori\HeyMan\StartGuarding;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * @var Uploader
     */
    private $uploader;
    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
        EnsureFileIdExists::install();
        resolve(StartGuarding::class)->start();
    }
    public function index()
    {
        $files = FileService::new()->allWithRelation();
        return FileResponse::index($files);
    }
    public function download($id)
    {
        HeyMan::checkPoint('EnsureFileIdExists');
        return FileService::new()->findByIdWithRelation($id)->download();
    }
    public function show($id)
    {
        HeyMan::checkPoint('EnsureFileIdExists');
        $file = FileService::new()->findByIdWithRelation($id);
        return FileResponse::show($file);
    }
    public function destroy($id)
    {
        HeyMan::checkPoint('EnsureFileIdExists');
        FileService::new()->findByIdWithRelation($id)->delete();
        return FileResponse::destroy();
    }
    public function store(Request $request)
    {
        try {
            $this->validateFile($request);
            $file = $this->uploader->upload();
            return response()->json(["status" => "success", "message" => 'فایل با موفقیت اپلود شد.', "file" => $file], Response::HTTP_CREATED);

            // return FileResponse::store($file);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);

            // return FileResponse::storeFailed($e->getMessage());
        }
    }
    private function validateFile($request)
    {
        Log::info('request file type: ' . $request->file->getMimeType());

        $request->validate([
            'file' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/svg+xml,video/mp4,application/zip,audio/webm,audio/wave,audio/wav,audio/x-wav,video/webm,audio/webm;codecs=opus,audio/mp4',]
        ]);
    }
}
