<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">
        <!-- Cabeçalho da Página -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Meu Perfil ✨</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Atualize sua foto e detalhes pessoais aqui.</p>
        </div>

        <!-- Cartão principal com o formulário -->
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-6 md:p-8">
            <div class="grid grid-cols-12 gap-6 lg:gap-8">

                <!-- Coluna da Esquerda: Foto de Perfil e Info -->
                <div class="col-span-12 lg:col-span-4">
                    <div class="flex flex-col items-center text-center p-4 lg:border-r border-gray-200 dark:border-gray-700/60 h-full">

                        <!-- Upload da Foto de Perfil -->
                        <div class="relative mb-4 group">
                            <img class="w-32 h-32 rounded-full object-cover shadow-lg"
                                 src="{{asset('storage/' . auth()->user()->profile_photo_path)}}"
                                 alt="Foto do Perfil">


                            <label for="profile-pic-upload"
                                   class="absolute inset-0 w-full h-full bg-black bg-opacity-50 rounded-full flex items-center justify-center text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <i class="bi bi-camera-fill text-2xl"></i>
                            </label>
                            <input id="profile-pic-upload" type="file" class="hidden">
                        </div>

                        <!-- Nome e Cargo do Usuário -->
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</h2>

                    </div>
                </div>

                <!-- Coluna da Direita: Formulário -->
                <div class="col-span-12 lg:col-span-8">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Informações da Conta</h3>
                    <form action="{{route('perfil.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <!-- Linha com Nome Completo e Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300" for="name">Nome Completo</label>
                                    <div class="relative">
                                        <i class="bi bi-person-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input id="name"  class="form-input w-full pl-10" type="text" value="{{ auth()->user()->name }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300" for="email">Endereço de Email</label>
                                    <div class="relative">
                                        <i class="bi bi-envelope-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input id="email" name="email" class="form-input w-full pl-10" type="email" value="{{ auth()->user()->email }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300" for="email">Foto</label>
                                    <div class="relative">
                                        <i class="bi bi-envelope-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input id="foto" name="foto" class="form-input w-full pl-10" type="file" >
                                    </div>
                                </div>
                            </div>




                            <!-- Seção de Alteração de Senha -->
                            <div class="border-t border-gray-200 dark:border-gray-700/60 pt-6">
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-4">Alterar Senha</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300" for="password">Nova Senha</label>
                                        <div class="relative">
                                            <i class="bi bi-key-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input id="password" name="password" class="form-input w-full pl-10" type="password" autocomplete="new-password" placeholder="••••••••">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300" for="password_confirmation">Confirmar Nova Senha</label>
                                        <div class="relative">
                                            <i class="bi bi-key-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input id="password_confirmation" name="password_confirmation" class="form-input w-full pl-10" type="password" placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ações do Formulário -->
                        <div class="flex justify-end items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700/60">
                            <button type="button" class="btn bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">Cancelar</button>
                            <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white ml-3">
                                <i class="bi bi-check-circle-fill mr-2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Adicione estas classes ao seu arquivo CSS principal (ex: app.css) -->
<style>
    .form-input, .form-textarea {
        @apply bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition;
    }
    .btn {
        @apply font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out;
    }
</style>
