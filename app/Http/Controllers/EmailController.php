<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use App\Models\Regra;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Http;

class EmailController extends Controller
{
    public $client;

    public function __construct()
    {
        $this->client = Client::account('default');
    }

    public function connect(): JsonResponse
    {
        // Every client has to connect to the server before any operation can be performed.
        try {
            $this->client->connect();
            return response()->json([
                'message' => 'Connected successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to connect',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function isConnected(): JsonResponse
    {
        // Check if the current connection is still established.
        try {
            $status = $this->client->isConnected();
            return response()->json([
                'connected' => $status
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check connection',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function checkConnection(): JsonResponse
    {
        // Check if the current connection is still alive and reconnect if necessary.
        try {
            $this->client->checkConnection();
            return response()->json([
                'message' => 'Connection is alive'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check connection',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function reconnect(): JsonResponse
    {
        // You can force the client to do a reconnect. This will close the connection and open a new one.
        try {
            $this->client->reconnect();
            return response()->json([
                'message' => 'Reconnected successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reconnect',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function disconnect(): JsonResponse
    {
        // You can force the client to close the current connection.
        try {
            $this->client->disconnect();
            return response()->json([
                'message' => 'Disconnected successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to disconnect',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function getFolders(): JsonResponse
    {
        // If hierarchical order is set to true, it will make a tree of folders, otherwise it will return flat array.
        try {
            $folders = $this->client->getFolders(true);
            // Convert the folder collection to a more suitable format for JSON response
            $folderList = $folders->map(function ($folder) {
                return [
                    'name' => $folder->name,
                    'full_name' => $folder->full_name,
                    // 'id' => $folder->id,
                    'messages_count' => $folder->messages()->all()->count(),
                ];
            });
            return response()->json([
                'folders' => $folderList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve folders',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getMessages(): JsonResponse
    {
        try {
            $folders = $this->client->getFolders(true);
            $messagesList = [];
            foreach ($folders as $folder) {
                $messages = $folder->messages()->get();
                foreach ($messages as $message) {
                    // var_dump($message->getAttributes());
                    $messagesList[] = [
                        'uid' => $message->get('uid'),
                        'subject' => $message->get('subject')[0],
                        'date' => $message->get('date')[0],
                        'from' => $message->get('from')[0]->mail,
                        'body' => $message->getTextBody() ?: 'Body vazio',
                    ];
                }
            }
            return response()->json([
                'messages' => $messagesList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve messages',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function buscaSubject()
    {
        $subject = 'Nenhuma mensagem encontrada';
        try {
            $this->client->checkConnection();
            $folders = $this->client->getFolders(true);
            foreach ($folders as $folder) {
                $firstMessage = $folder->messages()->get()->first();
                if ($firstMessage) {
                    $subject = $firstMessage->get('subject')[0];
                    try {
                        $firstMessage->delete($expunge = true);
                        Log::create([
                            'processo' => __FUNCTION__,
                            'detalhe' => 'Mensagem com subject (' . $subject . ') encontrada.'
                        ]);
                    } catch (\Exception $deleteException) {
                        Log::create([
                            'processo' => __FUNCTION__,
                            'detalhe' => 'Erro ao excluir a mensagem: ' . $deleteException->getMessage()
                        ]);
                    }
                    return $subject;
                }
            }
            Log::create([
                'processo' => __FUNCTION__,
                'detalhe' => 'Nenhuma mensagem encontrada'
            ]);
        } catch (\Exception $e) {
            Log::create([
                'processo' => __FUNCTION__,
                'detalhe' => $e->getMessage()
            ]);
        }
        return $subject;
    }

    public function executaTarefas()
    {
        $log = ['processo' => __FUNCTION__];
        $http_status = 500;
        try {
            $subject = $this->buscaSubject();
            $regra = Regra::where('subject', $subject)->where('status', 'ativado')->first();
            if ($regra) {
                // $response = Http::get($regra->webhook);
                $response = Http::withoutVerifying()->get($regra->webhook); //essa forma desabilita o SSL
                $log['detalhe'] = $response->successful() ? 'Response webhook sucesso' : 'Response webhook error';
                $http_status = $response->successful() ? 200 : 500;
            } else {
                $log['detalhe'] = 'Nenhuma regra ativada encontrada para o subject: ' . $subject;
                $http_status = 200;
            }
        } catch (\Exception $e) {
            $log['detalhe'] = 'Erro: ' . $e->getMessage();
        }
        Log::create($log);
        return response()->json($log, $http_status);
    }

    public function deleteMessage($message)
    {
        $message = $message->delete($expunge = true);
    }


    public function enviarEmail(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string',
                'body' => 'required|string',
                "from_nome" => 'required|string',
                "from_email" => 'required|email'
            ]);

            $dados = $request->all();

            Mail::to($dados['to'])->send(new Email($dados));

            return response()->json([
                'message' => 'Email enviado com sucesso',
                'data' => $dados
            ], 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json([
                'message' => 'Email não enviado',
                'error' => $error
            ], 500);
        }
    }
}
