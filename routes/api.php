<?php


use App\Http\Controllers\SendMailController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::put('/sign-up/{token}', [AuthController::class,'signup']);
Route::post('/verify-pin', [AuthController::class,'verifypin']);
Route::post('/admin-login',[AdminAuthController::class,'adminlogin']);





Route::group(['middleware' => ['auth:sanctum']], function () {

Route::post('/logout', [AdminAuthController::class,'logout']);
Route::post('/send-email', [SendMailController::class,'sendmail']);

Route::post('/update-profile/{id}', [AuthController::class,'updateprofile']);
    

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
