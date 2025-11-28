<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Título -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Novo Usuário</h1>
            </div>
        </div>

        <!-- Formulário -->
        <!-- Adicionei a variável 'phone' ao x-data e a função de máscara -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6"
             x-data="{
                selectAllLojas: false,
                selectAllGrupos: false,
                phone: '{{ old('telefone') }}',
                maskPhone() {
                    let v = this.phone.replace(/\D/g, '').slice(0, 11);
                    if (v.length >= 11) {
                        this.phone = `(${v.slice(0,2)}) ${v.slice(2,7)}-${v.slice(7)}`;
                    } else if (v.length >= 7) {
                        this.phone = `(${v.slice(0,2)}) ${v.slice(2,6)}-${v.slice(6)}`;
                    } else if (v.length >= 3) {
                        this.phone = `(${v.slice(0,2)}) ${v.slice(2)}`;
                    } else {
                        this.phone = v;
                    }
                }
             }"
             x-init="maskPhone()"> <!-- Formata caso venha do old() -->

            <form method="POST" action="{{ route('usuario.store') }}">
                @csrf

                <!-- Nome -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full form-input dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                           required autofocus>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="mt-1 block w-full form-input dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                           required>
                </div>

                <!-- Telefone (Com Máscara) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>

                    <!-- Input Visível (Com Máscara) - Sem 'name' para não enviar ao backend -->
                    <input type="text"
                           x-model="phone"
                           @input="maskPhone()"
                           placeholder="(99) 99999-9999"
                           maxlength="15"
                           class="mt-1 block w-full form-input dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100">

                    <!-- Input Oculto (Apenas Números) - Este é o que vai para o banco -->
                    <input type="hidden" name="phone" :value="phone.replace(/\D/g, '')">
                </div>

                <!-- Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                    <input type="password" name="password"
                           class="mt-1 block w-full form-input dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                           required>
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Senha</label>
                    <input type="password" name="password_confirmation"
                           class="mt-1 block w-full form-input dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                           required>
                </div>

                <div class="mb-4">
                    <input type="checkbox" class="form-checkbox text-blue-600 dark:bg-gray-800 dark:border-gray-600" name="type" value="1" id="ativo" class="mr-2">
                    <label for="ativo" class="text-gray-700 dark:text-gray-300">Vendedor</label>
                </div>

                <!-- Checkbox Lojas -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Lojas</label>
                    <div class="mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="selectAllLojas" @click="selectAllLojas = !selectAllLojas"
                                   class="form-checkbox text-blue-600 dark:bg-gray-800 dark:border-gray-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Marcar todas</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($lojas as $loja)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="lojas[]" value="{{ $loja->id }}"
                                       x-bind:checked="selectAllLojas"
                                       class="form-checkbox text-blue-600 dark:bg-gray-800 dark:border-gray-600">
                                <span class="ml-2 text-gray-800 dark:text-gray-100">{{ $loja->nome }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Checkbox Grupos -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Grupos</label>
                    <div class="mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="selectAllGrupos" @click="selectAllGrupos = !selectAllGrupos"
                                   class="form-checkbox text-blue-600 dark:bg-gray-800 dark:border-gray-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Marcar todos</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($grupos as $grupo)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="grupos[]" value="{{ $grupo->id }}"
                                       x-bind:checked="selectAllGrupos"
                                       class="form-checkbox text-blue-600 dark:bg-gray-800 dark:border-gray-600">
                                <span class="ml-2 text-gray-800 dark:text-gray-100">{{ $grupo->nome }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-start gap-4">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        Salvar
                    </button>
                    <a href="{{ route('usuario.index') }}">
                        <button type="button"
                                class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 transition">
                            Cancelar
                        </button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
