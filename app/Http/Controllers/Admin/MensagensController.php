<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
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
            // Garantir que o número comece com 55
            $number = preg_replace('/^\+?0?/', '', $number); // remove + ou 0 no início
            $number = '55' . $number;
    
            Log::info('Enviando mensagem mídia para Evolution API', [
                'number' => $number,
                'fileUrl' => $fileUrl,
                'fileName' => $fileName,
                'caption' => $caption,
                'mediaType' => $mediaType,
                'mimeType' => $mimeType,
            ]);
    
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => '95596EA594FF-4B7F-BAF9-6A8CB1CE039E',
            ])->post('http://192.168.1.9:8080/message/sendMedia/digisat', [
                'number' =>$number,
                'delay' => 10000,
                'mediatype' => $mediaType,
                'mimetype' => $mimeType,
                'caption' => $caption,
                'media' => $fileUrl,
                'fileName' => $fileName,
            ]);
    
            Log::info('Resposta da Evolution API', ['response' => $response->body()]);
    
            return $response->json();
    
        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem mídia', [
                'message' => $e->getMessage(),
                'number' => $number,
                'fileUrl' => $fileUrl,
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
