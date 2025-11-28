<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Contas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $query = Clientes::query();

        // Filtros simples
        if ($request->filled('codigo')) {
            $query->where('codigo', $request->codigo);
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('loja')) {
            $query->where('loja', 'like', '%' . $request->loja . '%');
        }

        if ($request->filled('profissao')) {
            $query->where('profissao', 'like', '%' . $request->profissao . '%');
        }

        // Paginação
        $clientes = $query->paginate(25)->withQueryString();

        return view('admin.clinetes.index', compact('clientes'));
    }
    public function sync(Request $request)
    {
        $clientes = $request->all(); // Array de clientes

        Log::info('Iniciando sincronização em lote de clientes.', ['request' => $clientes]);

        $resultados = [];

        foreach ($clientes as $clienteData) {
            try {
                // Validação individual
                $validated = validator($clienteData, [
                    'codigo' => 'required|integer',
                    'nome' => 'required|string',
                    'email' => 'nullable|email',
                    'telefone' => 'nullable|string',
                    'loja' => 'required|string',
                    'valor_ultimacompra' => 'nullable|numeric',
                    'data_ultimacompra' => 'nullable|date',
                    'vendedor' => 'nullable|string',
                    'profissao' => 'nullable|string',
                    'cidade' => 'nullable|string',
                    'estado' => 'nullable|string',
                ])->validate();

                $cliente = Clientes::updateOrCreate(
                    [
                        'codigo' => $validated['codigo'],
                        'loja' => $validated['loja'],
                    ],
                    [
                        'nome' => $validated['nome'],
                        'email' => $validated['email'] ?? null,
                        'telefone' => $validated['telefone'] ?? null,
                        'valor_ultimacompra' => $validated['valor_ultimacompra'] ?? null,
                        'data_ultimacompra' => $validated['data_ultimacompra'] ?? null,
                        'vendedor' => $validated['vendedor'] ?? null,
                        'profissao' => $validated['profissao'] ?? null,
                        'cidade' => $validated['cidade'] ?? null,
                        'estado' => $validated['estado'] ?? null,
                    ]
                );

                Log::info('Cliente sincronizado com sucesso.', ['cliente' => $cliente->codigo]);

                $resultados[] = [
                    'codigo' => $cliente->codigo,
                    'status' => 'ok'
                ];
            } catch (\Exception $e) {
                Log::error('Erro ao sincronizar cliente.', [
                    'dados' => $clienteData,
                    'erro' => $e->getMessage()
                ]);

                $resultados[] = [
                    'codigo' => $clienteData['codigo'] ?? 'desconhecido',
                    'status' => 'erro',
                    'mensagem' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Processo de sincronização finalizado.',
            'resultados' => $resultados
        ]);
    }

    public function negociar(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'data_fim' => 'required|date'
        ]);

        $cliente = Clientes::find($request->cliente_id);

        // Pega todas as contas do cliente
        $contas = Contas::where('codigo_cliente', $cliente->codigo)->get();

        // Atualiza cada conta individualmente
        foreach ($contas as $conta) {
            $conta->data_vencimento = $request->data_fim;
            $conta->data_envio = $request->data_fim;
            $conta->status = 3;
            $conta->update();
        }

        return redirect()->route('cliente.index')->with('success', 'Negociação registrada com sucesso.');
    }



}
