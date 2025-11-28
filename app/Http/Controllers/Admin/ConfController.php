<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feirido_Loja;
use App\Models\Feriado;
use App\Models\Instance;
use App\Models\Instancia;
use App\Models\Lojas;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConfController extends Controller
{


    public function index()
    {

       $this->fetchWhatsAppInstance();
        $feriados = DB::table('feriado')
            ->join('feriado_loja', 'feriado_loja.feriado_id', '=', 'feriado.id')
            ->join('lojas', 'lojas.id', '=', 'feriado_loja.loja_id')
            ->select(
                'feriado.id',
                'feriado.descricao',
                'feriado.data',
                DB::raw('GROUP_CONCAT(lojas.nome ORDER BY lojas.nome SEPARATOR ", ") as lojas')
            )
            ->groupBy('feriado.id', 'feriado.descricao', 'feriado.data')
            ->get();

        $instancia = Instancia::all();
        $lojas = Lojas::all();

        // Recupera dados do .env via config/mail.php
        $emailSettings = [
            'mailer' => config('mail.mailer'),
            'host' => config('mail.host'),
            'port' => config('mail.port'),
            'username' => config('mail.username'),
            'password' => config('mail.password'),
            'encryption' => config('mail.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return view('admin/configuracao/index', compact('feriados', 'instancia', 'lojas', 'emailSettings'));
    }


    public function createFeriado(Request $request)
    {


        // Criar o feriado
        $feriado = new Feriado();
        $feriado->descricao = $request->input('descricao');
        $feriado->data = $request->input('data');
        $feriado->save();

        // Salvar a relação com várias lojas
        $lojaIds = $request->input('loja'); // deve ser um array de IDs


        if (is_array($lojaIds)) {
            foreach ($lojaIds as $lojaId) {
                $loja = new Feirido_Loja();
                $loja->loja_id = $lojaId;
                $loja->feriado_id = $feriado->id;
                $loja->save();
            }
        }

        return  redirect()->route('conf.index') ->with('success' ,'Feriado criado com sucesso.');
    }

    public function updateFeriado(Request $request, $id)
    {

        $feriado = Feriado::find($id);
        $feriado->descricao = $request->input('descricao');
        $feriado->data = $request->input('data');
        $feriado->save();

        // Deleta todas as relações antigas desse feriado
        Feirido_Loja::where('feriado_id', $feriado->id)->delete();

        // Reinsere as novas lojas
        $lojaIds = $request->input('loja'); // array de IDs
        if (is_array($lojaIds)) {
            foreach ($lojaIds as $lojaId) {
                $loja = new Feirido_Loja();
                $loja->loja_id = $lojaId;
                $loja->feriado_id = $feriado->id;
                $loja->save();
            }
        }
        return  redirect()->route('conf.index') ->with('success' ,'Feriado atualizado com sucesso.');
    }

    public function deleteFeriado($id)
    {
        try {
            $feriado = Feriado::findOrFail($id);
            $feriado->delete();


            Feirido_Loja::where('feriado_id', $feriado->id)->delete();
            return response()->json(['message' => 'Feriado excluído com sucesso!'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json(['message' => 'Feriado não encontrado.'], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Ocorreu um erro ao excluir o feriado.'], 500);
        }
    }

    public function fetchWhatsAppInstance()
    {
        $instancias = Instancia::all();


        if ($instancias->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma instância encontrada.',
                'data' => []
            ], 404);
        }

        $resultados = [];

        foreach ($instancias as $instancia) {

            // Requisição para a API com cabeçalho correto
            $response = Http::withHeaders([
                'apikey' => $instancia->hash
            ])->get("https://evolutionapi.digisatdistribuidora.com/instance/fetchInstances", [
                'instanceName' => $instancia->instanceName
            ]);

            if ($response->successful()) {
                $dados = $response->json();


                if (is_array($dados)) {
                    foreach ($dados as $item) {

                        if (isset($item['name']) && $item['name'] === $instancia->instanceName) {
                            $statusAPI = $item['connectionStatus'] ?? 'unknown';


                            $instancia->status = $statusAPI;
                            $instancia->save();

                            $resultados[] = [
                                'nome' => $instancia->nome,
                                'status' => $statusAPI
                            ];
                        }
                    }
                }
            } else {
                $resultados[] = [
                    'nome' => $instancia->nome,
                    'erro' => 'Erro ao consultar API: ' . $response->status()
                ];
            }
        }

        return response()->json([
            'message' => 'Status das instâncias atualizados.',
            'data' => $resultados
        ]);
    }

    public function logoutInstance(string $instanceName)
    {
        try {
            $instancia = Instancia::where('instanceName', $instanceName)->firstOrFail();

            $apiUrl = 'https://evolutionapi.digisatdistribuidora.com/instance/logout/' . $instancia->instanceName;

            $response = Http::withHeaders([
                'apikey' => $instancia->hash,
            ])->delete($apiUrl);

            if ($response->successful()) {
                $instancia->status = 'closed';
                $instancia->save();

                Log::info("Instância deslogada com sucesso", [
                    'instanceName' => $instanceName,
                    'instanceId' => $instancia->id,
                ]);

                return redirect()->route('conf.index')
                    ->with('success', 'Instância deslogada com sucesso.');
            }

            $errorResponse = $response->json() ?? $response->body();

            Log::error("Erro ao desconectar WhatsApp: " . json_encode($errorResponse));

            return redirect()->route('conf.index')
                ->with('error', 'Erro ao desconectar o WhatsApp: ' . json_encode($errorResponse));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('conf.index')
                ->with('error', 'Instância não encontrada.');

        } catch (\Exception $e) {
            Log::error("Erro inesperado ao deslogar instância {$instanceName}: " . $e->getMessage());

            return redirect()->route('conf.index')
                ->with('error', 'Ocorreu um erro inesperado ao deslogar a instância.');
        }
    }

    public function connectWhatsAppInstance(string $instanceName)
    {
        try {
            $instancia = Instancia::where('instanceName', $instanceName)->firstOrFail();

            $url = "https://evolutionapi.digisatdistribuidora.com/instance/connect/{$instanceName}";

            $response = Http::withHeaders([
                'apikey' => $instancia->hash,
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Armazena o base64 do QR Code na sessão
                return redirect()->route('conf.index')->with([
                    'qrcode' => $data['base64'] ?? null,
                    'instanceName' => $instanceName,
                ]);
            }

            return redirect()->route('conf.index')->with('erro', 'Erro ao conectar: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Erro ao conectar instância: ' . $e->getMessage());

            return redirect()->route('conf.index')->with('erro', 'Erro inesperado ao conectar.');
        }
    }

    public function updateInstance(Request $request, $id)
    {
        $request->validate([
            'instanceName' => 'required|string|max:255',
            'hash' => 'required|string|max:255',
            // Adicione outras validações conforme necessário
        ]);

        try {
            $instancia = Instancia::findOrFail($id);

            $instancia->update([
                'instanceName' => $request->instanceName,
                'hash' => $request->hash,
                'url' => $request->url, // Exemplo se quiser editar a URL
                // Adicione aqui outros campos se necessário
            ]);

            return redirect()->route('conf.index')
                ->with('success', 'Instância atualizada com sucesso.');

        } catch (\Exception $e) {
            Log::error("Erro ao atualizar instância: " . $e->getMessage());
            return redirect()->route('conf.index')
                ->with('error', 'Erro ao atualizar a instância.');
        }
    }





}
