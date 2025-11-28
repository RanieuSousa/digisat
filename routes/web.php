<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ClientesController;
use App\Http\Controllers\Admin\UsuariosController;
use \App\Http\Controllers\Admin\ConfController;
use \App\Http\Controllers\Admin\ContasController;
use App\Http\Controllers\Admin\ClientesPotenciaisController;
use App\Http\Controllers\Admin\MensagensController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');
Route::middleware('auth')->get('/dashboard', [ClientesController::class, "index"])->name('dashboard');

Route::middleware('auth')->get('clientes',[ClientesController::class, "index"])->name('cliente.index');
Route::middleware('auth')->post('cliente/negociar',[ClientesController::class, "negociar"])->name('cliente.negociar');

Route::middleware('auth')->get('usuarios',[UsuariosController::class, "index"])->name('usuario.index');
Route::middleware('auth')->get('criar/usuario',[UsuariosController::class, "create"])->name('usuario.create');
Route::middleware('auth')->post('store/usuario',[UsuariosController::class, "store"])->name('usuario.store');
Route::middleware('auth')->get('editar/usuario/{id}',[UsuariosController::class, "edit"])->name('usuario.edit');
Route::middleware('auth')->put('atualizar/usuario/{id}',[UsuariosController::class, "update"])->name('usuario.update');
Route::middleware('auth')->delete('deletar/usuario/{id}',[UsuariosController::class, "destroy"])->name('usuario.destroy');

Route::middleware('auth')->get('perfil',[UsuariosController::class, "perfil"])->name('perfil.index');
Route::middleware('auth')->post('update/perfil',[UsuariosController::class, "updatePerfil"])->name('perfil.update');

Route::middleware('auth')->get('configuracao',[ConfController::class, "index"])->name('conf.index');
Route::middleware('auth')->get('desconetar/whastsapp/{instanceName}',[ConfController::class, "logoutInstance"])->name('logout.instance');
Route::middleware('auth')->get('conectar/whastsapp/{instanceName}',[ConfController::class, "connectWhatsAppInstance"])->name('connect.instance');
Route::middleware('auth')->post('create/feriado',[ConfController::class, "createFeriado"])->name('create.feriado');
Route::middleware('auth')->put('update/feriado/{id}',[ConfController::class, "updateFeriado"])->name('update.feriado');
Route::middleware('auth')->delete('/feriados/delete/{id}', [ConfController::class, 'deleteFeriado'])->name('delete.feriado');
Route::put('/update/instancia/{id}', [App\Http\Controllers\Admin\ConfController::class, 'updateInstance'])->name('instancia.update');

Route::middleware('auth')->get('contas',[ContasController::class, "index"])->name('contas.index');

Route::middleware('auth')->get('clientes/potenciais',[ClientesPotenciaisController::class, "index"])->name('potenciais.index');
Route::middleware('auth')->get('/admin/clientespotenciais', [ClientesPotenciaisController::class, 'index'])->name('admin.clientespotenciais.index');



Route::middleware('auth')->get('mensagens',[MensagensController::class, "index"])->name('mensagens.index');
Route::middleware('auth')->put('update/mensagens',[MensagensController::class, "update"])->name('mensagem.update');


Route::middleware('auth')->get('enviar/mensagens',[MensagensController::class, "messagememMassa"])->name('mensagens.enviarEmMassa');
Route::middleware('auth')->post('enviar',[MensagensController::class, "enviarmensagem"])->name('send');

Route::get('enviar/cobraca',[ContasController::class, "enviarmensagem"]);



