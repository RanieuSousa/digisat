<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('syn/clientes',[\App\Http\Controllers\Admin\ClientesController::class, 'sync']);
Route::post('syn/contas',[\App\Http\Controllers\Admin\ContasController::class, 'syncContas']);
Route::get('bi',[\App\Http\Controllers\Admin\UsuariosController::class, 'usuarios']);
Route::get('funcionario/{name}',[\App\Http\Controllers\Admin\UsuariosController::class, 'funcionario']);
Route::get('flutuante/{name}',[\App\Http\Controllers\Admin\UsuariosController::class, 'flutuanete']);
