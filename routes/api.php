<?php

use App\Models\Cafe;
use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Http\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('files')->controller(App\Http\Controllers\FileController::class)->group(
    function (Router $router) {
        $router->post('/upload', 'store');
    }
);

Route::post('/diaries', function (Request $request) {
    $request->validate([
        'file_id' => 'required|exists:files,id',
        'cafe_id' => 'required|exists:cafes,id',
        'name' => 'required|string',
        'email' => 'nullable|email',
    ]);
    $ip = $request->header('x-forwarded-for');
    // if user ip is not set in header
    if (!$ip) {
        // get user ip by server remote address
        $ip = $request->server('REMOTE_ADDR');
    }
    // return user ip
    // slice before ,
    $ip = explode(',', $ip)[0];
    $data = request()->only(['file_id', 'cafe_id', 'name', 'email']) + ['ip' => $ip];
    $diary = Diary::create($data);
    return response()->json(["status" => "success", "diary" => $diary], Response::HTTP_CREATED);
});

Route::get('/diaries/{cafe}', function (Cafe $cafe) {
    // order desc
    return response()->json(["status" => "success", "diaries" => $cafe->diaries()->orderBy('created_at', 'desc')->get()], Response::HTTP_CREATED);
});
Route::get('/like/diary/{diary}', function (Diary $diary) {
    $ip = request()->header('x-forwarded-for');
    // if user ip is not set in header
    if (!$ip) {
        // get user ip by server remote address
        $ip = request()->server('REMOTE_ADDR');
    }
    // return user ip
    // slice before ,
    $ip = explode(',', $ip)[0];
    if ($diary->ip == $ip) {
        return response()->json(["status" => "error", "message" => 'کاربر نمیتواند خاطره خود را لایک کند.'], Response::HTTP_BAD_REQUEST);
    }
    if (!$diary->is_like) {
        \DB::table('diary_like')->insert([
            'diary_id' => $diary->id,
            'ip' => $ip,
            'created_at' => now()
        ]);
        return response()->json(["status" => "success", "diary" => $diary], Response::HTTP_CREATED);
    }
    return response()->json(["status" => "error", "message" => 'لایک کاربر قبلا ثبت شده است.'], Response::HTTP_BAD_REQUEST);
});
