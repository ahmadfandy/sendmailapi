<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailSendController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\ApprovalController as ApprovalController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/statis', function () {
//     return view('emails.statis');
// });

Route::get('/changestatus/{status}/{email}', [EmailSendController::class, 'changestatus']);
Route::get('kirim-email', [EmailSendController::class, 'sendingMail'])->name('send.email');

Route::get('/getdate', [EmailSendController::class, 'getdate']);

Route::get('/testing', [TestingController::class, 'index']);

Route::get('/statis', [TestingController::class, 'gambar']);