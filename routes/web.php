<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RegraController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view ('welcome');
})->name('home');

// Grupo de rotas para operações com as Regras
Route::resource('regra', RegraController::class);

// Grupo de rotas para operações com os Logs
Route::resource('log', LogController::class);

// Grupo de rotas para operações com o Mailo
Route::prefix('mailo')->group(function () {

    // Rotas para conexão e verificação de estado da conexão
    Route::get('/isconnected', [EmailController::class, 'isConnected'])->name('mailo.isConnected');
    Route::get('/connect', [EmailController::class, 'connect'])->name('mailo.connect');
    Route::get('/checkconnection', [EmailController::class, 'checkConnection'])->name('mailo.checkConnection');
    Route::get('/reconnect', [EmailController::class, 'reconnect'])->name('mailo.reconnect');
    Route::get('/disconnect', [EmailController::class, 'disconnect'])->name('mailo.disconnect');

    // Rotas para manipulação de pastas e mensagens
    Route::get('/getfolders', [EmailController::class, 'getFolders'])->name('mailo.getFolders');
    Route::get('/getmessages', [EmailController::class, 'getMessages'])->name('mailo.getMessages');

    // Rota para enviar e-mails
    Route::post('/enviaremail', [EmailController::class, 'enviarEmail'])->name('mailo.enviarEmail');
});

// Rota para obter o token CSRF
Route::get('/csrf-token', function () {
    return csrf_token();
})->name('csrf.token');

Route::get('/monitorar', [EmailController::class, 'executaTarefas'])->name('monitorar');

Route::get('/testewebhook', function () {
    return response()->json([
        'message' => 'get para webhook teste OK'
    ], 200);
})->name('testewebhook');
