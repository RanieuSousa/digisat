<div class="overflow-x-auto rounded-lg shadow-md">
    <form method="GET" class="mb-4 flex flex-wrap gap-4">
        <input type="text" name="codigo" value="{{ request('codigo') }}" placeholder="Código"
               class="border dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-1 rounded" />

        <input type="text" name="codigo_venda" value="{{ request('codigo_venda') }}" placeholder="Código Venda"
               class="border dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-1 rounded" />

        <input type="text" name="codigo_cliente" value="{{ request('codigo_cliente') }}" placeholder="Código Cliente"
               class="border dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-1 rounded" />

        <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Nome Cliente"
               class="border dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-1 rounded" />

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">
            Filtrar
        </button>

        <a href="{{ route(Route::currentRouteName()) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">
            Limpar
        </a>
    </form>


    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-800">
        <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código Vendas</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código do Cliente</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Cliente</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Valor</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data Vencimento</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Enviar mensagens</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Loja</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>

        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-800">
        @foreach($avencer as $contas)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $contas->codigo }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $contas->codigo_venda }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $contas->codigo_cliente }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $contas->nome }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    R$ {{ number_format($contas->valor, 2, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($contas->data_vencimento)->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($contas->data_envio)->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $contas->loja }}</td>
                <td class="px-6 py-4 text-sm">
                    @php
                        $statusClasses = [
                            1 => 'text-orange-500',
                            2 => 'text-green-500',
                            3 => 'text-blue-500',
                            4 => 'text-red-500',
                        ];

                        $statusText = [
                            1 => 'Aguardando envio',
                            2 => 'Mensagem enviada',
                            3 => 'Em negociação',
                            4 => 'Erro no envio da mensagem',
                        ];
                    @endphp

                    <span class="{{ $statusClasses[$contas->status] ?? 'text-gray-500' }}">
        {{ $statusText[$contas->status] ?? 'Desconhecido' }}
    </span>
                </td>

            </tr>
        @endforeach


        </tbody>
    </table>

    <div class="mt-4">
        {{ $avencer->links() }}
    </div>

</div>
