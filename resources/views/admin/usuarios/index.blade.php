<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full" x-data="deleteModal()" x-cloak>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <strong>Ocorreram alguns erros:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Título -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Usuários</h1>
            </div>
            <div>
                <a href="{{ route('usuario.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                    + Adicionar Usuário
                </a>
            </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grupos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lojas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ação</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"> <img class="w-20 h-20 rounded-full object-cover shadow-lg"
                                                                                                                   src="{{asset('storage/' . $usuario->profile_photo_path)}}"
                                                                                                                   alt="Foto do Perfil"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $usuario->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $usuario->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                @php
                                    $grupos = $usuario->grupos->pluck('nome')->toArray();
                                    $chunks = array_chunk($grupos, 5);
                                @endphp

                                @foreach ($chunks as $chunk)
                                    @foreach ($chunk as $grupo)
                                        <span class="inline-block mr-2 mb-1 px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs">{{ $grupo }}</span>
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                @php
                                    $lojas = $usuario->lojas->pluck('nome')->toArray();
                                    $chunks = array_chunk($lojas, 2);
                                @endphp

                                @foreach ($chunks as $chunk)
                                    @foreach ($chunk as $loja)
                                        <span class="inline-block mr-2 mb-1 px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs">{{ $loja }}</span>
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 flex gap-2">

                                <!-- Botão Editar -->
                                <a href="{{ route('usuario.edit', $usuario->id) }}"
                                   class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded hover:bg-yellow-500 transition"
                                   title="Editar">
                                    Editar
                                </a>

                                <!-- Botão Deletar -->
                                <button type="button"
                                        @click="open({{ $usuario->id }}, '{{ $usuario->name }}')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                        title="Deletar">
                                    Deletar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <div x-show="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
             x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirmar Exclusão</h2>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                    Tem certeza que deseja deletar o usuário <strong x-text="userName"></strong>?
                </p>

                <div class="flex justify-end gap-3">
                    <button @click="close()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancelar
                    </button>

                    <form method="POST" :action="`{{ url('deletar/usuario') }}/${userId}`">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function deleteModal() {
            return {
                show: false,
                userId: null,
                userName: '',
                open(id, name) {
                    this.userId = id;
                    this.userName = name;
                    this.show = true;
                },
                close() {
                    this.show = false;
                    this.userId = null;
                    this.userName = '';
                }
            }
        }
    </script>
</x-app-layout>
