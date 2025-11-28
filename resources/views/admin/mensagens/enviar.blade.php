<x-app-layout>

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
    <div x-data="{ files: [], messageContent: '' }" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Título -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Envio de Mensagem em Massa</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Envie mensagens para clientes segmentados no WhatsApp.</p>
            </div>
        </div>


        <!-- 2. Layout principal dividido em duas colunas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Coluna Esquerda: Formulário -->
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 md:p-8">
                <form action="{{ route('send') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Seção de Filtros -->
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">1. Filtrar Destinatários</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Filtro de Loja -->
                            <div>
                                <label for="loja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
                                <select id="loja" name="loja" class="form-select w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md">
                                    <option value="">Todas</option>
                                    @foreach($lojas ?? [] as $loja)
                                        <option value="{{ $loja }}" {{ old('loja') == $loja ? 'selected' : '' }}>{{ $loja }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Vendedor -->
                            <div>
                                <label for="vendedor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendedor</label>
                                <select id="vendedor" name="vendedor" class="form-select w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md">
                                    <option value="">Todos</option>
                                    @foreach($vendedores ?? [] as $vendedor)
                                        <option value="{{ $vendedor }}" {{ old('vendedor') == $vendedor ? 'selected' : '' }}>{{ $vendedor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Profissão -->
                            <div>
                                <label for="profissao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profissão</label>
                                <select id="profissao" name="profissao" class="form-select w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md">
                                    <option value="">Todas</option>
                                    @foreach($profissoes ?? [] as $profissao)
                                        <option value="{{ $profissao }}" {{ old('profissao') == $profissao ? 'selected' : '' }}>{{ $profissao }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Cidade -->
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                                <select id="cidade" name="cidade" class="form-select w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md">
                                    <option value="">Todas</option>
                                    @foreach($cidades ?? [] as $cidade)
                                        <option value="{{ $cidade }}" {{ old('cidade') == $cidade ? 'selected' : '' }}>{{ $cidade }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                                <select id="estado" name="estado" class="form-select w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md">
                                    <option value="">Todos</option>
                                    @foreach($estados ?? [] as $estado)
                                        <option value="{{ $estado }}" {{ old('estado') == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Seção da Mensagem -->
                    <fieldset class="mt-8">
                        <legend class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">2. Conteúdo da Mensagem</legend>
                        <div>
    <label for="mensagem" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Mensagem
    </label>
    <p class="text-red-600 text-xs mb-2">
        usar a chave [nome] para enviar com o nome do cliente
    </p>
    <textarea
        id="mensagem"
        name="mensagem"
        rows="5"
        x-model="messageContent"
        class="form-textarea w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 rounded-md"
        placeholder="Digite sua mensagem aqui..."
    >{{ old('mensagem') }}</textarea>
</div>

                    </fieldset>

                    <!-- Seção de Anexos -->
                    <fieldset class="mt-8">
                        <legend class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">3. Anexos</legend>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" name="anexos[]" id="anexos" class="hidden" multiple @change="files = [...$event.target.files].map(f => f.name)">
                            <label for="anexos" class="cursor-pointer text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                Clique para selecionar fotos, vídeos ou documentos
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Você pode selecionar múltiplos arquivos.</p>
                        </div>
                    </fieldset>

                    <!-- Botão de Envio -->
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800">
                            Enviar Mensagens
                        </button>
                    </div>
                </form>
            </div>

            <!-- Coluna Direita: Pré-visualização -->
            <div class="hidden lg:block">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Pré-visualização</h2>
                <div class="w-full max-w-sm mx-auto bg-gray-800 rounded-3xl p-2 shadow-2xl">
                    <div class="bg-cover bg-center h-[600px] rounded-2xl" style="background-image: url('https://i.pinimg.com/736x/8c/98/99/8c98994518b575bfd8c949e91d20548b.jpg');">
                        <div class="flex flex-col h-full p-4">
                            <!-- Cabeçalho do Chat -->
                            <div class="flex items-center bg-gray-700 p-2 rounded-t-lg -mx-4 -mt-4">
                                <div class="w-10 h-10 bg-gray-500 rounded-full mr-3"></div>
                                <div class="flex-1">
                                    <p class="text-white font-semibold">Nome do Cliente</p>
                                    <p class="text-xs text-gray-300">online</p>
                                </div>
                            </div>

                            <!-- Corpo do Chat -->
                            <div class="flex-1 pt-4 flex flex-col justify-end">
                                <!-- Bolha da Mensagem -->
                                <div class="flex justify-end mb-2">
                                    <div class="bg-[#dcf8c6] dark:bg-[#056162] text-black dark:text-white rounded-lg p-3 max-w-xs" style="border-top-right-radius: 0;">
                                        <!-- 4. Mostra o texto do textarea em tempo real -->
                                        <p class="text-sm" x-text="messageContent || 'Sua mensagem aparecerá aqui...'"></p>

                                        <!-- 5. Mostra a pré-visualização dos arquivos -->
                                        <div x-show="files.length > 0" class="mt-2 border-t border-gray-500 pt-2">
                                            <template x-for="file in files" :key="file">
                                                <div class="flex items-center text-xs text-gray-600 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 p-2 rounded-md mb-1">
                                                    <!-- Ícone de anexo -->
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    <span class="truncate" x-text="file"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="text-right text-xs text-gray-500 mt-1">15:30</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
