<x-app-layout>
    <!-- 1. Adicione o x-data aqui, no container principal -->
    <div x-data="{ showModal: false, selectedClienteId: null }" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Título -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Clientes</h1>
            </div>
        </div>

        <!-- Filtros -->
        <form method="GET" action="{{ route('cliente.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="codigo" value="{{ request('codigo') }}"
                       class="form-input w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                       placeholder="Código">

                <input type="text" name="nome" value="{{ request('nome') }}"
                       class="form-input w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                       placeholder="Nome">

                <input type="text" name="loja" value="{{ request('loja') }}"
                       class="form-input w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                       placeholder="Loja">

                <input type="text" name="profissao" value="{{ request('profissao') }}"
                       class="form-input w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                       placeholder="Profissão">
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Filtrar
                </button>
                <a href="{{ route('cliente.index') }}"
                   class="ml-3 text-sm text-gray-600 dark:text-gray-400 hover:underline">Limpar filtros</a>
            </div>
        </form>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Última Compra</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profissão</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ação</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $cliente->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $cliente->nome}}</td>
                            @php
                                $telefone = $cliente->telefone ?? '';
                                if (strlen($telefone) === 11) {
                                    $telefoneFormatado = '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
                                } else {
                                    $telefoneFormatado = $telefone;
                                }
                            @endphp

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $telefoneFormatado ?: '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $cliente->loja ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $cliente->data_ultimacompra ? \Carbon\Carbon::parse($cliente->data_ultimacompra)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $cliente->vendedor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $cliente->profissao }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <button
                                    type="button"
                                    @click="showModal = true; selectedClienteId = {{ $cliente->id }}"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    Negociação
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum cliente encontrado.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $clientes->links() }}
        </div>

        <!-- Modal -->
        <!-- 2. O div do x-data que estava aqui foi REMOVIDO. O modal agora está dentro do escopo principal -->
        <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showModal = false" class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Encerrar negociação</h2>
                <form method="POST" action="{{ route('cliente.negociar') }}">
                    @csrf
                    <input type="hidden" name="cliente_id" :value="selectedClienteId">

                    <label for="data_fim" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de fim da negociação:</label>
                    <input type="date" name="data_fim" id="data_fim" required class="form-input w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4">

                    <div class="flex justify-end">
                        <button type="button" @click="showModal = false" class="mr-2 text-gray-500 hover:text-gray-700">Cancelar</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
