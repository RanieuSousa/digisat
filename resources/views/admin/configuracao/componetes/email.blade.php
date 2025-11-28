<form action="#" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow-md dark:bg-gray-800">
    @csrf

    <div>
        <label for="mailer" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Mailer</label>
        <input type="text" name="mailer" id="mailer" value="{{ $emailSettings['mailer'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="host" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Host</label>
        <input type="text" name="host" id="host" value="{{ $emailSettings['host'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="port" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Porta</label>
        <input type="number" name="port" id="port" value="{{ $emailSettings['port'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Usuário</label>
        <input type="text" name="username" id="username" value="{{ $emailSettings['username'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Senha</label>
        <input type="password" name="password" id="password" value="{{ $emailSettings['password'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Criptografia</label>
        <input type="text" name="encryption" id="encryption" value="{{ $emailSettings['encryption'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">E-mail Remetente</label>
        <input type="email" name="from_address" id="from_address" value="{{ $emailSettings['from_address'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div>
        <label for="from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nome do Remetente</label>
        <input type="text" name="from_name" id="from_name" value="{{ $emailSettings['from_name'] }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" />
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Salvar Configurações
        </button>
    </div>
</form>
