<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="{ tab: 'whatsapp' }">
        <!-- Título -->
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                <div class="flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 font-bold">
                        &times;
                    </button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif


        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Configurações</h1>
            </div>
        </div>

        <!-- Abas -->
        <div class="mb-6 border-b border-gray-300 dark:border-gray-600">
            <nav class="flex space-x-6" aria-label="Tabs">
                <button @click="tab = 'whatsapp'"
                        :class="tab === 'whatsapp' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white'"
                        class="flex items-center space-x-2 py-2 px-4 border-b-2 font-medium text-sm">
                    <i class="bi bi-whatsapp text-lg"></i>
                    <span>Whatsapp</span>
                </button>

                <button @click="tab = 'email'"
                        :class="tab === 'email' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white'"
                        class="flex items-center space-x-2 py-2 px-4 border-b-2 font-medium text-sm">
                    <i class="bi bi-envelope text-lg"></i>
                    <span>Email</span>
                </button>

                <button @click="tab = 'feriados'"
                        :class="tab === 'feriados' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white'"
                        class="flex items-center space-x-2 py-2 px-4 border-b-2 font-medium text-sm">
                    <i class="bi bi-calendar-event text-lg"></i>
                    <span>Feriados</span>
                </button>
            </nav>
        </div>

        <!-- Conteúdo das Abas -->
        <div>
            <div x-show="tab === 'whatsapp'">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-white mb-4">Configurações de Whatsapp</h2>
               @include('admin/configuracao/componetes/whatsapp')
            </div>

            <div x-show="tab === 'email'" x-cloak>
                <h2 class="text-xl font-semibold text-gray-700 dark:text-white mb-4">Configurações de Email</h2>
                @include('admin/configuracao/componetes/email')
            </div>

            <div x-show="tab === 'feriados'" x-cloak>
                <h2 class="text-xl font-semibold text-gray-700 dark:text-white mb-4">Gerenciar Feriados</h2>
                @include('admin/configuracao/componetes/feriados')
            </div>
        </div>
    </div>
</x-app-layout>
