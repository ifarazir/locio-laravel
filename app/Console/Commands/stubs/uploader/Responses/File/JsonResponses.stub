<?php

namespace App\Http\Responses\Admin\File;

use Illuminate\Http\Response;

class JsonResponses
{
    public function index($files)
    {
        return response()->json(["status" => "success", "files" => $files], Response::HTTP_OK);
    }

    public function invalidFileId()
    {
        return response()->json(["status" => "error", "message" => __('message.invalidId', ['model' => __('message.model.file')])], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function show($file)
    {
        return response()->json(["status" => "success", "file" => $file], Response::HTTP_OK);
    }

    public function store($file)
    {
        return response()->json(["status" => "success", "message" => __('message.store', ['model' => __('message.model.file')]), "file" => $file], Response::HTTP_CREATED);
    }

    public function update($file)
    {
        return response()->json(["status" => "success", "message" => __('message.update', ['model' => __('message.model.file')]), "file" => $file], Response::HTTP_ACCEPTED);
    }

    public function destroy()
    {
        return response()->json(["status" => "success", "message" => __('message.destroy', ['model' => __('message.model.file')])], Response::HTTP_ACCEPTED);
    }

    public function storeFailed($message)
    {
        return response()->json(["status" => "error", "message" => $message], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function updateFailed()
    {
        return response()->json(["status" => "error", "message" => __('message.updateFailed', ['model' => __('message.model.file')])], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function destroyFailed()
    {
        return response()->json(["status" => "error", "message" => __('message.destroyFailed', ['model' => __('message.model.file')])], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
