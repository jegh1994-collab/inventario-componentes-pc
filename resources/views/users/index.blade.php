<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Administración de Usuarios') }}
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                + Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes de Estado / Error -->
            @if (session('status'))
                <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-800 dark:bg-emerald-950 dark:border-emerald-800 dark:text-emerald-200 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('error'))
                <div class="p-4 bg-rose-100 border border-rose-400 text-rose-800 dark:bg-rose-950 dark:border-rose-800 dark:text-rose-200 rounded-lg">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            @if ($user->id === Auth::id())
                                                <span class="text-xs text-indigo-500 font-semibold">(Tú)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <form action="{{ route('users.role', $user) }}" method="POST" class="inline-flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:ring-indigo-500" {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                                    <option value="warehouse" {{ $user->role === 'warehouse' ? 'selected' : '' }}>Almacenista</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($user->is_active)
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if ($user->id !== Auth::id())
                                                <form action="{{ route('users.toggle', $user) }}" method="POST" class="inline" onsubmit="return confirm('¿Confirmas cambiar el estado de este usuario?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs font-semibold underline {{ $user->is_active ? 'text-rose-600 hover:text-rose-900 dark:text-rose-400' : 'text-emerald-600 hover:text-emerald-900 dark:text-emerald-400' }}">
                                                        {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No editable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
