<?php

use Illuminate\Support\Facades\Route;


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
    $event=\Illuminate\Support\Facades\DB::table("events")->where("name","SAMI-AEC")->first();
    return view('index',compact("event"));
});
Route::post("save",[\App\Http\Controllers\HomeController::class,"save"])->name("save");
Route::get("check-email",[\App\Http\Controllers\HomeController::class,"check_email"])->name("check_email");


Route::middleware(['auth'])->group(function () {
    Route::get("dashboard",[\App\Http\Controllers\AdminController::class,"index"])->name("dashboard.index");

    Route::view("qr-code","admin.qr")->name("qr");
Route::get("checked_in/{id?}",[\App\Http\Controllers\AdminController::class,"checked_in"])->name("checked_in");
Route::view("register-attendance","admin.register_attendance")->name("register_attendance");
Route::get("search-on-ticket",[\App\Http\Controllers\AdminController::class,"search_on_ticket"])->name("search_on_ticket");
Route::get("attendance-list",[\App\Http\Controllers\AdminController::class,"attendance_list"])->name("attendance_list");
Route::get("statistics",[\App\Http\Controllers\AdminController::class,"statistics"])->name("statistics");
Route::get("export",[\App\Http\Controllers\AdminController::class,"export"])->name("export");

    Route::get("emps",[\App\Http\Controllers\AdminController::class,"all_emps"])->name("emps");
    Route::post("resendTickets",[\App\Http\Controllers\HomeController::class,"resendTickets"])->name("resendTickets");

});

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
