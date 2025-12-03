<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Instancia;
use App\Models\Mensagens;
use http\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MensagensController extends Controller
{
    public  function index()
    {
        $mensagem  = Mensagens::all();
        return view('admin/mensagens/index', compact('mensagem'));

    }

    public function update(Request $request)
    {
        // Validação básica dos dados recebidos
        $validated = $request->validate([
            'id' => 'required|exists:mensagens,id',
            'mensagem' => 'required|string|max:1000',
        ]);

        // Busca e atualização em uma linha
        $mensagem = Mensagens::find($validated['id']);
        $mensagem->update([
            'mensagem' => $validated['mensagem'],
        ]);

        // Redireciona com mensagem de sucesso
        return redirect()->back()->with('success', 'Mensagem atualizada com sucesso!');
    }

    public function messagememMassa()
    {

        // BUSQUE APENAS OS DADOS NECESSÁRIOS PARA OS FILTROS
        $lojas = Clientes::whereNotNull('loja')->distinct()->pluck('loja')->sort();
        $vendedores = Clientes::whereNotNull('vendedor')->distinct()->pluck('vendedor')->sort();
        $profissoes = Clientes::whereNotNull('profissao')->distinct()->pluck('profissao')->sort();
        $cidades = Clientes::whereNotNull('cidade')->distinct()->pluck('cidade')->sort();
        $estados = Clientes::whereNotNull('estado')->distinct()->pluck('estado')->sort();

        // Passe as variáveis otimizadas para a view
        return view('admin/mensagens/enviar', compact(
            'lojas',
            'vendedores',
            'profissoes',
            'cidades',
            'estados'
        ));
    }


    public function enviarmensagem(Request $request)
    {
        Log::info('🔥 Entrou na função enviarmensagem', ['request_all' => $request->all()]);
        $clientes = Clientes::query()
            ->when($request->filled('loja'), fn($query) => $query->where('loja', $request->loja))
            ->when($request->filled('vendedor'), fn($query) => $query->where('vendedor', $request->vendedor))
            ->when($request->filled('profissao'), fn($query) => $query->where('profissao', $request->profissao))
            ->when($request->filled('cidade'), fn($query) => $query->where('cidade', $request->cidade))
            ->when($request->filled('estado'), fn($query) => $query->where('estado', $request->estado))
            ->limit(1)
            ->get();


        $mensagemOriginal = $request->input('mensagem');
        $anexos = $request->file('anexos');

        $dadosExtras = [
            'data-vencimento' => $request->input('data_vencimento'),
            'valor' => $request->input('valor'),
        ];

        foreach ($clientes as $cliente) {
            $telefone = $cliente->telefone;
            $mensagemTratada = $this->tratarMensagemComVariaveis($mensagemOriginal, $cliente, $dadosExtras);

            if ($anexos && is_array($anexos) && count($anexos) > 0) {
                foreach ($anexos as $anexo) {
                    $path = $anexo->store('anexos_temp', 'public');
                    $url = asset("storage/$path");

                    $mimeType = $anexo->getMimeType();
                    $mediaType = $this->detectarMediaType($mimeType);

                    $this->sendMediaMessage(
                        $telefone,
                        $url,
                        $anexo->getClientOriginalName(),
                        $mensagemTratada,
                        $mediaType,
                        $mimeType
                    );
                }
            } else {
                // Se não houver mídia, não envia mensagem
                // Ou se quiser pode lançar um erro ou aviso aqui
            }
        }

        return redirect()->back()->with('success', 'Mensagens enviadas com sucesso!');
    }
 private function detectarMediaType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        return 'document';
    }
    public function sendMediaMessage($number, $fileUrl, $fileName, $caption, $mediaType, $mimeType)
    {
        try {

            // Normaliza número
            $originalNumber = $number;
            $number = preg_replace('/^\+?0?/', '', $number);
            $number = '55' . $number;

            $instancia = Instancia::find(6);

            // Monta URL final
            $url = $instancia->url . '/message/sendMedia/' . $instancia->instanceName;

            // Monta payload final enviado
            $payload = [
                'number'    => $number,
                'delay'     => 10000,
                'mediatype' => $mediaType,
                'mimetype'  => $mimeType,
                'caption'   => $caption,
                'media'     => $fileUrl,
                'fileName'  => $fileName,
            ];

            // LOGS ANTES DO ENVIO
            Log::info('🚀 Enviando mídia para Evolution API', [
                'url'      => $url,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'apikey'       => $instancia->hash,
                ],
                'payload'  => $payload,
                'original_number' => $originalNumber,
                'normalized_number' => $number,
            ]);

            // Envio da requisição
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey'       => $instancia->hash,
            ])->post($url, $payload);

            // LOGS COMPLETOS DA RESPOSTA
            Log::info('📥 Resposta da Evolution API', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'headers'  => $response->headers(),
                'successful' => $response->successful(),
                'failed'     => $response->failed(),
                'json'     => $response->json(),
            ]);

            return $response->json();

        } catch (\Exception $e) {

            // LOG EM CASO DE ERRO NA REQUISIÇÃO
            Log::error('❌ Erro ao enviar mídia para Evolution API', [
                'error_message' => $e->getMessage(),
                'number'        => $number ?? null,
                'payload'       => $payload ?? null,
                'url'           => $url ?? null,
                'trace'         => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }




    private function tratarMensagemComVariaveis($mensagem, $cliente, $dadosExtras = [])
    {
        $variaveis = [
            '[nome]' => $cliente->nome ?? '',
            '[telefone]' => $cliente->telefone ?? '',
            '[cidade]' => $cliente->cidade ?? '',
        ];

        foreach ($dadosExtras as $chave => $valor) {
            $variaveis["[$chave]"] = $valor;
        }

        return str_replace(array_keys($variaveis), array_values($variaveis), $mensagem);
    }


}
