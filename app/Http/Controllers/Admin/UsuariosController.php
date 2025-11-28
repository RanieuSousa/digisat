<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Grupos;
use App\Models\GruposUsuarios;
use App\Models\Lojas;
use App\Models\LojasUsuarios;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuariosController extends Controller
{
    public  function  index()
    {


        $usuarios = User::with('grupos')->get();
        return view('admin/usuarios/index',compact('usuarios'));

    }

    public function create()
    {
        $clientes = Clientes::all();

        foreach ($clientes as $cliente) {
            $vendedorNome = trim($cliente->vendedor);
            if (!empty($vendedorNome)) {
                $existe = Grupos::where('nome', $vendedorNome)->exists();
                if (!$existe) {
                    $grupo = new Grupos();
                    $grupo->nome = $vendedorNome;
                    $grupo->save();
                }
            }
        }
        foreach ($clientes as $cliente) {
            $lojaNome = trim($cliente->loja);
            if (!empty($lojaNome)) {
                $existe = Lojas::where('nome', $lojaNome)->exists();
                if (!$existe) {
                    $novaLoja = new Lojas();
                    $novaLoja->nome = $lojaNome;
                    $novaLoja->save();
                }
            }
        }

        $grupos = Grupos::all();
        $lojas = Lojas::all();

        return view("admin.usuarios.create", compact('grupos', 'lojas'));
    }

    public function store(Request $request)
    {
        // Validação
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'phone' => 'nullable|string',
            'type' => 'nullable|integer',
            'lojas' => 'array',
            'grupos' => 'array',
        ]);

        // Criar usuário
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->type = $request->type;
        $usuario->phone = $request->phone;
        $usuario->password = Hash::make($request->password);
        $usuario->save();


        if ($request->filled('lojas')) {
            foreach ($request->lojas as $lojaId) {
                LojasUsuarios::create([
                    'usuario_id' => $usuario->id,
                    'loja_id' => $lojaId,
                ]);
            }
        }

        // Relacionar grupos
        if ($request->filled('grupos')) {
            foreach ($request->grupos as $grupoId) {
                GruposUsuarios::create([
                    'usuario_id' => $usuario->id,
                    'grupo_id' => $grupoId,
                ]);
            }
        }

        return redirect()->route('usuario.index')->with('success', 'Usuário criado com sucesso.');
    }
    public function edit($id)
    {
        $usuario = User::with(['grupos', 'lojas'])->findOrFail($id);
        $grupos = Grupos::all();
        $lojas = Lojas::all();

        return view("admin.usuarios.edit", compact('usuario', 'grupos', 'lojas'));
    }

    public function update(Request $request, $id)
    {


        Log::debug('Iniciando update de usuário', ['user_id' => $id]);
        Log::debug('Dados recebidos', $request->all());

        try {
            // Validação protegida
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
                'phone' => 'nullable|string',
                'lojas' => 'array',
                'type' => 'nullable|integer',
                'grupos' => 'array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('Erro de validação', [
                'errors' => $ve->errors()
            ]);
            return redirect()->back()->withErrors($ve->errors())->withInput();
        }

        try {
            $usuario = User::findOrFail($id);
            Log::debug('Usuário encontrado', ['usuario' => $usuario]);

            $usuario->name = $request->name;
            $usuario->email = $request->email;
            $usuario->phone = $request->phone;
            $usuario->type = $request->type;

            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
                Log::debug('Senha atualizada');
            }
            $usuario->save();
            Log::debug('Usuário atualizado com sucesso');

            // Lojas
            LojasUsuarios::where('usuario_id', $usuario->id)->delete();
            if ($request->filled('lojas')) {
                foreach ($request->lojas as $lojaId) {
                    LojasUsuarios::create([
                        'usuario_id' => $usuario->id,
                        'loja_id' => $lojaId,
                    ]);
                }
                Log::debug('Lojas atualizadas', ['lojas' => $request->lojas]);
            }

            // Grupos
            GruposUsuarios::where('usuario_id', $usuario->id)->delete();
            if ($request->filled('grupos')) {
                foreach ($request->grupos as $grupoId) {
                    GruposUsuarios::create([
                        'usuario_id' => $usuario->id,
                        'grupo_id' => $grupoId,
                    ]);
                }
                Log::debug('Grupos atualizados', ['grupos' => $request->grupos]);
            }

            return redirect()->route('usuario.index')->with('success', 'Usuário atualizado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', [
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile()
            ]);

            return redirect()->back()->withErrors('Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $usuario = User::findOrFail($id);

            // Remove relacionamentos de lojas e grupos
            LojasUsuarios::where('usuario_id', $usuario->id)->delete();
            GruposUsuarios::where('usuario_id', $usuario->id)->delete();

            // Deleta o usuário
            $usuario->delete();

            return redirect()->route('usuario.index')->with('success', 'Usuário deletado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('usuario.index')->withErrors('Erro ao deletar usuário: ' . $e->getMessage());
        }
    }
    public function usuarios()
    {
        return User::select('name', 'profile_photo_path')->get()->toJson();
    }
    public function perfil()
    {
 return view('admin/usuarios/perfil');

    }
    public function updatePerfil(Request $request)
    {
        $usuario = User::find(Auth::id());

        // Atualiza sempre o e-mail
        $usuario->email = $request->email;
        $usuario->phone = $request->phone;

        // Atualiza a senha apenas se for enviada
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }

        // Salva a foto se enviada
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $path = $request->file('foto')->store('fotos_usuarios', 'public');
            $usuario->profile_photo_path = $path;
        }

        $usuario->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function funcionario($name)
    {
        $usuario = User::where('name', 'like', '%' . $name . '%')->first();
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }
        return response()->json($usuario);
    }

    public function flutuanete($loja)
    {

        $usuarios = User::whereHas('grupos', function ($query) {
            $query->where('nome', 'Flutuante');
        })
            ->whereHas('lojas', function ($query) use ($loja) {
                $query->where('nome', $loja);
                // Dica: Se quiser busca parcial use: $query->where('nome', 'like', '%' . $loja . '%');
            })
            ->get();

        return response()->json($usuarios);
    }

}
