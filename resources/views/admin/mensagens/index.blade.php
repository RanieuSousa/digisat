<x-app-layout>
    <!-- 1. Inicializa o Alpine.js com as variáveis para controlar o modal de edição -->
    <div x-data="{ showEditModal: false, editingMessage: {} }" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Título -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Mensagens</h1>
            </div>
        </div>

        <!-- Tabela de Mensagens -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mensagem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ação</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($mensagem as $msg)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{$msg->mensagem}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm
                            @if($msg->tipo == 1) text-green-600 dark:text-green-400
                            @elseif($msg->tipo == 2) text-orange-600 dark:text-orange-400
                            @else text-gray-900 dark:text-gray-100
                            @endif">
                                {{ $msg->tipo == 1 ? 'Aviso' : ($msg->tipo == 2 ? 'Cobrança' : $msg->tipo) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <!-- 2. Botão para abrir o modal, passando os dados da mensagem atual -->
                                <button
                                    type="button"
                                    @click="showEditModal = true; editingMessage = {{ json_encode($msg) }}"
                                    class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-yellow-600 dark:hover:bg-yellow-700 focus:outline-none dark:focus:ring-yellow-800">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de Edição -->
        <div x-show="showEditModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             style="display: none;">
            <div @click.away="showEditModal = false" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Editar Mensagem</h2>

                <form method="POST" action="{{ route('mensagem.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editingMessage.id">
                    <div>
                        <label for="mensagem_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Mensagem:
                        </label>
                        <textarea name="mensagem" id="mensagem_edit" x-model="editingMessage.mensagem" rows="4" required
                                  class="form-input w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white mb-4 rounded"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" @click="showEditModal = false"
                                class="mr-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
