{{-- Exibe o QR Code se existir na sessão --}}
@if (session('qrcode'))
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
            <h2 class="text-lg font-bold mb-4">Escaneie o QR Code para conectar</h2>

            <img src="{{ session('qrcode') }}" alt="QR Code" class="w-full">

            <a href="{{ route('conf.index') }}" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">
                &times;
            </a>
        </div>
    </div>
@endif

{{-- Tabela Principal --}}
<div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg mt-4">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nome</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Loja</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ações</th>
        </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        {{-- Iteração das instâncias (usando $item para evitar conflito de nomes) --}}
        @foreach($instancia as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $item->instanceName }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if ($item->status === 'open')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Conectado
                        </span>
                    @elseif ($item->status === 'closed' || $item->status === 'close')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Desconectado
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ ucfirst($item->status) }}
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ $item->loja ?? '-' }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                    {{-- Botão EDITAR (Abre o Modal) --}}
                    <button type="button"
                            onclick="openEditModal('{{ $item->id }}', '{{ $item->instanceName }}', '{{ $item->hash }}', '{{ $item->url ?? '' }}')"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Editar
                    </button>

                    {{-- Botões de Ação (Conectar/Desconectar) --}}
                    @if ($item->status === 'open')
                        <a href="{{ route('logout.instance', $item->instanceName) }}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            Desconectar
                        </a>
                    @elseif ($item->status === 'closed' || $item->status === 'close')
                        <form method="GET" action="{{ route('connect.instance', $item->instanceName) }}" class="inline">
                            <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                Conectar
                            </button>
                        </form>
                    @else
                        {{-- Status desconhecido/intermediário: mostra ambos --}}
                        <form method="GET" action="{{ route('connect.instance', $item->instanceName) }}" class="inline">
                            <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-2">
                                Conectar
                            </button>
                        </form>
                        <a href="{{ route('logout.instance', $item->instanceName) }}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            Desconectar
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- Modal de Edição (Fica fora da tabela e oculto por padrão) --}}
<div id="editInstanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-lg relative m-4">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Editar Instância</h2>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formEditInstance" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="edit_instanceName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome da Instância</label>
                    <input type="text" name="instanceName" id="edit_instanceName" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2 border">
                </div>

                <div>
                    <label for="edit_hash" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hash (API Key)</label>
                    <input type="text" name="hash" id="edit_hash" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2 border">
                </div>

                <div>
                    <label for="edit_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL</label>
                    <input type="text" name="url" id="edit_url"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2 border">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts para controle do Modal --}}
<script>
    function openEditModal(id, name, hash, url) {
        const modal = document.getElementById('editInstanceModal');

        // Preencher os campos
        document.getElementById('edit_instanceName').value = name;
        document.getElementById('edit_hash').value = hash;
        document.getElementById('edit_url').value = url || '';

        // Definir a URL de ação do formulário
        // Ajuste o prefixo '/admin/instancia/' conforme sua rota real definida no web.php
        const form = document.getElementById('formEditInstance');
        form.action = `/admin/instancia/${id}`;

        // Exibir o modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex'; // Garante que o flex seja aplicado para centralizar
    }

    function closeEditModal() {
        const modal = document.getElementById('editInstanceModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    // Fechar ao clicar fora do conteúdo do modal
    window.onclick = function(event) {
        const modal = document.getElementById('editInstanceModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
