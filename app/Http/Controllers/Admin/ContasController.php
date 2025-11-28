<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contas;
use App\Models\Feriado;
use App\Models\Instancia;
use App\Models\Lojas;
use App\Models\Mensagens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ContasController extends Controller
{

    public function index(Request $request)
{
    // Lojas únicas do usuário
    $lojas = Lojas::join('lojas_usuarios', 'lojas_usuarios.loja_id', '=', 'lojas.id')
        ->where('lojas_usuarios.usuario_id', auth()->user()->id)
        ->pluck('lojas.nome')
        ->unique()
        ->values();

    // Contas + clientes, evitando duplicados
    $query = Contas::select('contas.*', 'clientes.nome')
        ->join('clientes', 'clientes.codigo', '=', 'contas.codigo_cliente')
        ->whereIn('contas.loja', $lojas)
        ->where('contas.tipo', 2)
        ->distinct();

    // Filtros
    if ($request->filled('codigo')) {
        $query->where('contas.codigo', $request->codigo);
    }
    if ($request->filled('codigo_venda')) {
        $query->where('contas.codigo_venda', $request->codigo_venda);
    }
    if ($request->filled('codigo_cliente')) {
        $query->where('contas.codigo_cliente', $request->codigo_cliente);
    }
    if ($request->filled('nome')) {
        $query->where('clientes.nome', 'like', '%' . $request->nome . '%');
    }

    // Paginação (mantém o nome esperado na view)
    $venciadas = $query->paginate(25);

    return view('admin.contas.index', compact('venciadas'));
}

    

public function syncContas(Request $request)
{
    $payload = $request->all();
    Log::info('Recebendo requisição para syncContas', ['payload_size' => count($payload)]);

    if (!is_array($payload)) {
        return response()->json(['status' => 'erro', 'mensagem' => 'Payload inválido. O corpo da requisição deve ser um array.'], 400);
    }

    $validadas = [];
    $erros = [];
    $codigosRecebidos = [];

    foreach ($payload as $index => $item) {
        try {
            // 1. Valida os dados de cada item do payload
            $data = Validator::make($item, [
                'codigo' => 'required|integer',
                'codigo_venda' => 'nullable|integer',
                'codigo_cliente' => 'nullable|integer',
                'valor' => 'nullable|numeric',
                'data_vencimento' => 'nullable|date',
                'data_envio' => 'nullable|date',
                'cilcus' => 'nullable', // Corrigido para 'cilcus' conforme seu validador original
                'tipo' => 'nullable|integer',
                'loja' => 'nullable|string',
            ])->validate();

            $codigosRecebidos[] = $data['codigo'];

            // 2. Lógica de "Criar ou Atualizar" de forma segura
            // Busca por 'codigo' ou instancia um novo objeto (sem salvar)
            $conta = Contas::firstOrNew(['codigo' => $data['codigo']]);

            // 3. Preenche os dados que devem ser sempre atualizados
            $conta->fill([
                'codigo_venda' => $data['codigo_venda'] ?? null,
                'codigo_cliente' => $data['codigo_cliente'] ?? null,
                'valor' => $data['valor'] ?? null,
                'data_vencimento' => $data['data_vencimento'] ?? null,
                'tipo' => $data['tipo'] ?? null,
                'loja' => $data['loja'] ?? null,
            ]);

            // 4. Se o modelo ainda não existe no banco, preenche os dados de criação
            // A propriedade 'exists' é 'false' para novos modelos.
            if (!$conta->exists) {
                $conta->data_envio = $data['data_envio'] ?? null;
                $conta->cilcus = $data['cilcus'] ?? 1; // Usa 'cilcus' do payload ou 1 como padrão
            }

            // 5. Salva o registro (executa INSERT ou UPDATE)
            $conta->save();
            
            // Adiciona o registro salvo/atualizado à lista de sucesso
            $validadas[] = $conta->toArray();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Erro específico de validação
            $erros[] = [
                'index' => $index,
                'erro' => 'Dados inválidos.',
                'detalhes' => $e->errors(),
                'dados' => $item
            ];
            Log::error('Erro de validação ao processar conta', ['index' => $index, 'erro' => $e->errors(), 'dados' => $item]);

        } catch (\Exception $e) {
            // Captura qualquer outra exceção (banco de dados, etc.)
            $erros[] = [
                'index' => $index,
                'erro' => $e->getMessage(),
                'dados' => $item
            ];
            Log::error('Erro geral ao processar conta', ['index' => $index, 'erro' => $e->getMessage(), 'dados' => $item]);
        }
    }

    // Bloco de limpeza (pós-processamento)

    // ✅ Remove duplicadas (mantém apenas a mais recente por 'codigo')
    $codigosDuplicados = Contas::select('codigo')
        ->groupBy('codigo')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('codigo');

    $duplicadasRemovidas = 0;
    foreach ($codigosDuplicados as $codigo) {
        // Pega o ID do registro mais recente que queremos manter
        $idParaManter = Contas::where('codigo', $codigo)->latest('id')->first()->id;
        
        // Deleta todos os outros com o mesmo 'codigo'
        $removidas = Contas::where('codigo', $codigo)->where('id', '!=', $idParaManter)->delete();
        $duplicadasRemovidas += $removidas;
    }

    // ✅ Remove registros antigos que não vieram no payload atual
    if (!empty($codigosRecebidos)) {
        $removidasAntigas = Contas::whereNotIn('codigo', $codigosRecebidos)->delete();
    } else {
        $removidasAntigas = 0;
    }

    return response()->json([
        'status' => 'concluído',
        'salvos_ou_atualizados' => count($validadas),
        'duplicadas_removidas' => $duplicadasRemovidas,
        'antigas_removidas' => $removidasAntigas,
        'erros' => $erros
    ], 200);
}



public function enviarmensagem()
{
    Log::info('Iniciando envio de mensagens.');

    $now = Carbon::now('America/Sao_Paulo');

    if ($now->isWeekend()) {
        Log::info('Execução interrompida: hoje é final de semana.');
        return;
    }

    $lojasComFeriado = Feriado::join('feriado_loja', 'feriado_loja.feriado_id', '=', 'feriado.id')
        ->join('lojas', 'lojas.id', '=', 'feriado_loja.loja_id')
        ->whereDate('feriado.data', $now->toDateString())
        ->pluck('lojas.nome');

    Log::info('Lojas com feriado hoje: ' . implode(', ', $lojasComFeriado->toArray()));

    // 1. Subconsulta para agregar dados da tabela 'contas'.
    // OS FILTROS DE CONTAS DEVEM ESTAR AQUI.
    $subqueryContas = DB::table('contas')
        ->select(
            'codigo_cliente',
            'loja',
            DB::raw('SUM(valor) as total_valor'),
            DB::raw('MIN(data_vencimento) as primeira_venc'),
            DB::raw('MAX(data_vencimento) as ultima_venc'),
            DB::raw('COUNT(id) as qtd_contas')
        )
        // ESTES FILTROS JÁ ESTAVAM NO LUGAR CORRETO:
        ->where('status', '!=', 3)
        ->where('tipo', '=', 2)
        ->where('cilcus', '<', 4)
        ->whereDate('data_envio', '<=', now()->toDateString())
        ->when($lojasComFeriado->isNotEmpty(), function ($query) use ($lojasComFeriado) {
            return $query->whereNotIn('loja', $lojasComFeriado);
        })
        ->groupBy('codigo_cliente', 'loja');

    // 2. Junte o resultado da subconsulta com a tabela de clientes.
    $contas = DB::table('clientes')
        ->joinSub($subqueryContas, 'contas_agrupadas', function ($join) {
            $join->on('clientes.codigo', '=', 'contas_agrupadas.codigo_cliente');
        })
        ->select(
            'clientes.codigo',
            'clientes.nome',
            'clientes.telefone',
            'contas_agrupadas.loja',
            'contas_agrupadas.total_valor',
            'contas_agrupadas.primeira_venc',
            'contas_agrupadas.ultima_venc',
            'contas_agrupadas.qtd_contas'
        )
       
        ->orderBy('clientes.nome')
        ->distinct()
        ->get();

  

    Log::info('Total de clientes encontrados para envio: ' . $contas->count());

    if ($contas->isEmpty()) {
        Log::info('Nenhum cliente encontrado para envio.');
        return;
    }

    $mensagem = Mensagens::find(2);

    foreach ($contas as $cliente) {
        Log::info("Processando cliente: {$cliente->nome} ({$cliente->codigo})");

        $textoTratado = $this->tratarMensagemComVariaveis($mensagem->mensagem, $cliente);
        Log::debug("Mensagem tratada: {$textoTratado}");

        $this->sendTextMessage($cliente->telefone, $textoTratado, $cliente->loja);

        $contasCliente = Contas::where('codigo_cliente', $cliente->codigo)->get();
        foreach ($contasCliente as $conta) {
            Log::debug("Atualizando conta ID {$conta->id} para cliente {$cliente->codigo}");
            $conta->cilcus = $conta->cilcus + 1;
            $conta->status = 2;
            if (empty($conta->data_envio)) {
                $conta->data_envio = Carbon::now('America/Sao_Paulo')->addDays(5)->format('Y-m-d');
            }
            $conta->save();
        }
    }

    Log::info('Envio de mensagens finalizado.');
}
    

   
    
    public function sendTextMessage($number, $text, $loja)
    {
        // Adiciona o 55 se não tiver
        if (!str_starts_with($number, '55')) {
            $number = '55' . preg_replace('/\D/', '', $number); // Remove caracteres não numéricos e adiciona 55
        }
    
        Log::info("Enviando mensagem para loja {$loja}, telefone {$number}");
    
        $instancia = Instancia::where('loja', $loja)->first();
    
        if (!$instancia) {
            Log::error("Instância não encontrada para a loja: {$loja}");
            return null;
        }
    
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $instancia->hash,
        ])->post(
            'https://evolutionapi.digisatdistribuidora.com/message/sendText/' . $instancia->instanceName,
            [
                'delay' => 10000,
                'text' => $text,
                'number' => $number, 
            ]
        );
    
        Log::info("Resposta do envio para {$number}:", $response->json() ?? []);
    
        return $response->json();
    }
    

    private function tratarMensagemComVariaveis($mensagem, $cliente, $dadosExtras = [])
    {
        $diasVencidos = 0;
        if (!empty($cliente->ultima_venc)) {
            $dataVenc = Carbon::parse($cliente->ultima_venc);
            $diasVencidos = $dataVenc->diffInDays(Carbon::now());
        }

        \Log::info('Dias vencidos calculados:', ['diasVencidos' => $diasVencidos]);

        $variaveis = [
            '[nome]' => $cliente->nome ?? '',
            '[valor]' => number_format($cliente->total_valor ?? 0, 2, ',', '.'),
            '[ultima_venc]' => (int) round($diasVencidos),
            '[qtd_contas]' => $cliente->qtd_contas ?? '',
        ];

        foreach ($dadosExtras as $chave => $valor) {
            $variaveis["[$chave]"] = $valor;
        }

        return str_replace(array_keys($variaveis), array_values($variaveis), $mensagem);
    }




}


