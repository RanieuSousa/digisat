<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ClientesPotenciaisController extends Controller
{


    public function index(Request $request)
    {
        $query = Clientes::query();

        if ($request->filled('codigo')) {
            $query->where('codigo', 'like', '%' . $request->codigo . '%');
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('loja')) {
            $query->where('loja', 'like', '%' . $request->loja . '%');
        }

        if ($request->filled('vendedor')) {
            $query->where('vendedor', 'like', '%' . $request->vendedor . '%');
        }

        if ($request->filled('profissao')) {
            $query->where('profissao', 'like', '%' . $request->profissao . '%');
        }

        // Filtro de clientes com mais de 30 dias desde última compra
        $query->whereDate('data_ultimacompra', '<=', now()->subDays(30));

        $clientes = $query->orderBy('data_ultimacompra', 'asc')->paginate(25)->appends($request->query());

        return view('admin/clientespostenciais/index', compact('clientes'));
    }



}


