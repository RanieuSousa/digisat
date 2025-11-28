

<div class="container mx-auto p-4 md:p-8">

    <div id="cabecalhoTabela" class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lista de Feriados</h1>
        <button id="btnAdicionar" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            <i class="bi bi-plus-circle"></i>
            Adicionar
        </button>
    </div>

    <div id="containerTabela">
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="hidden">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Loja(s)</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
                </thead>
                <tbody id="corpoTabela" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($feriados as $feriado)
                    <tr>
                        <td class="cell-id hidden">{{ $feriado->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{$feriado->descricao}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($feriado->data)->format('d/m') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{$feriado->lojas}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <button class="btn-editar text-blue-500 hover:text-blue-700 mr-2" title="Editar"><i class="bi bi-pencil"></i></button>
                            <button class="btn-deletar text-red-500 hover:text-red-700" title="Deletar"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="containerFormulario" class="hidden">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Adicionar Feriado</h2>
            <form id="formAdicao" action="{{ route('create.feriado') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                    <input type="text" id="descricao" name="descricao" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                    <input type="date" id="data" name="data" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja(s)</label>
                    <div id="loja-checkbox-group" class="space-y-2">
                        @foreach($lojas as $loja)
                            <div class="flex items-center">
                                <input id="loja-{{ $loja->id }}" name="loja[]" type="checkbox" value="{{ $loja->id }}" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-indigo-600">
                                <label for="loja-{{ $loja->id }}" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">{{ $loja->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" id="btnCancelar" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalEdicao" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative mx-auto mt-10 p-6 w-full max-w-xl bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Editar Feriado</h2>
        <form id="formEdicao" action="{{ route('update.feriado', ['id' => 'ID_PLACEHOLDER']) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_feriado_id" name="feriado_id">
            <div class="mb-4">
                <label for="edit_descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <input type="text" id="edit_descricao" name="descricao" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 shadow-sm" required>
            </div>
            <div class="mb-4">
                <label for="edit_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                <input type="date" id="edit_data" name="data" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 shadow-sm" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja(s)</label>
                <div id="edit_loja-checkbox-group" class="space-y-2">
                    @foreach($lojas as $loja)
                        <div class="flex items-center">
                            <input id="edit_loja-{{ $loja->id }}" name="loja[]" type="checkbox" value="{{ $loja->id }}" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-indigo-600">
                            <label for="edit_loja-{{ $loja->id }}" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">{{ $loja->nome }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" id="btnCancelarEdicao" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDeletar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <i class="bi bi-trash text-red-600 dark:text-red-300 text-2xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-2">Excluir Item</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="items-center px-4 py-3 gap-3 flex justify-center">
                <button id="btnCancelarDeletar" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-600">Cancelar</button>
                <button id="btnConfirmarDeletar" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-red-700">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Seletores de Elementos ---
        const containerTabela = document.getElementById('containerTabela');
        const containerFormulario = document.getElementById('containerFormulario');
        const modalEdicao = document.getElementById('modalEdicao');
        const modalDeletar = document.getElementById('modalDeletar');

        const btnAdicionar = document.getElementById('btnAdicionar');
        const btnCancelar = document.getElementById('btnCancelar');
        const btnCancelarEdicao = document.getElementById('btnCancelarEdicao');
        const btnCancelarDeletar = document.getElementById('btnCancelarDeletar');
        const btnConfirmarDeletar = document.getElementById('btnConfirmarDeletar');

        const formEdicao = document.getElementById('formEdicao');

        // --- Lógica para Adicionar ---
        btnAdicionar.addEventListener('click', () => {
            containerTabela.classList.add('hidden');
            containerFormulario.classList.remove('hidden');
        });
        btnCancelar.addEventListener('click', () => {
            containerTabela.classList.remove('hidden');
            containerFormulario.classList.add('hidden');
        });

        // --- Lógica de Ações da Tabela ---
        const corpoTabela = document.getElementById('corpoTabela');
        corpoTabela.addEventListener('click', function (event) {
            const botaoClicado = event.target.closest('button');
            if (!botaoClicado) return;

            const linha = botaoClicado.closest('tr');
            const feriadoId = linha.querySelector('.cell-id')?.textContent.trim();

            if (!feriadoId) return;

            // --- Abrir Modal de Edição ---
            if (botaoClicado.classList.contains('btn-editar')) {
                const descricao = linha.cells[1].textContent.trim();
                const dataTexto = linha.cells[2].textContent.trim();
                const lojasTexto = linha.cells[3].textContent.trim();

                let actionOriginal = "{{ route('update.feriado', ['id' => 'ID_PLACEHOLDER']) }}";
                formEdicao.setAttribute('action', actionOriginal.replace('ID_PLACEHOLDER', feriadoId));

                modalEdicao.querySelector('#edit_feriado_id').value = feriadoId;
                modalEdicao.querySelector('#edit_descricao').value = descricao;

                const [dia, mes] = dataTexto.split('/');
                if (dia && mes) {
                    const anoAtual = new Date().getFullYear();
                    modalEdicao.querySelector('#edit_data').value = `${anoAtual}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`;
                }

                const nomesLojasSalvas = lojasTexto.split(',').map(nome => nome.trim());
                modalEdicao.querySelectorAll('#edit_loja-checkbox-group input[type="checkbox"]').forEach(checkbox => {
                    const nomeLojaLabel = checkbox.nextElementSibling.textContent.trim();
                    checkbox.checked = nomesLojasSalvas.includes(nomeLojaLabel);
                });

                modalEdicao.classList.remove('hidden');
            }

            // --- Abrir Modal de Deleção ---
            if (botaoClicado.classList.contains('btn-deletar')) {
                btnConfirmarDeletar.setAttribute('data-id-deletar', feriadoId);
                modalDeletar.classList.remove('hidden');
            }
        });

        // --- Lógica para Fechar Modais ---
        btnCancelarEdicao.addEventListener('click', () => modalEdicao.classList.add('hidden'));
        btnCancelarDeletar.addEventListener('click', () => modalDeletar.classList.add('hidden'));

        // --- ✅ Lógica para Confirmar Deleção com Fetch ---
        btnConfirmarDeletar.addEventListener('click', async () => {
            const idParaDeletar = btnConfirmarDeletar.getAttribute('data-id-deletar');
            if (!idParaDeletar) return;

            let deleteUrl = "{{ route('delete.feriado', ['id' => 'ID_PLACEHOLDER']) }}";
            deleteUrl = deleteUrl.replace('ID_PLACEHOLDER', idParaDeletar);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    const linhaParaRemover = document.querySelector(`#corpoTabela .cell-id:where(:self:contains("${idParaDeletar}"))`)?.closest('tr');
                    if (linhaParaRemover) linhaParaRemover.remove();
                    modalDeletar.classList.add('hidden');
                } else {
                    alert(`Erro: ${result.message || 'Não foi possível excluir.'}`);
                }
            } catch (error) {
                console.error('Erro de rede:', error);
                alert('Não foi possível conectar ao servidor.');
            }
        });

        // NOTA: A lógica de submissão para ADICIONAR e EDITAR não foi implementada com fetch neste exemplo.
        // Você pode adicionar listeners de 'submit' para 'formAdicao' e 'formEdicao' seguindo o modelo
        // da requisição de deletar, mas usando os métodos 'POST' e 'POST' com FormData.
    });
</script>

