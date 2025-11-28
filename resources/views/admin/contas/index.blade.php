<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-full mx-auto" x-data="{ tab: 'abertas' }">

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
        @foreach($venciadas as $conta)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $conta->codigo }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $conta->codigo_venda }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $conta->codigo_cliente }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $conta->nome }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    R$ {{ number_format($conta->valor, 2, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::parse($conta->data_envio)->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $conta->loja }}</td>
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

                    <span class="{{ $statusClasses[$conta->status] ?? 'text-gray-500' }}">
        {{ $statusText[$conta->status] ?? 'Desconhecido' }}
    </span>
                </td>

            </tr>
        @endforeach


        </tbody>
    </table>

    <div class="mt-4">
        {{ $venciadas->links() }}
    </div>

</div>
  

    </div>
</x-app-layout>
