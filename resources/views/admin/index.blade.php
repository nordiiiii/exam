<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Управление пользователями</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(session('success')) <div class="text-green-600">{{ session('success') }}</div> @endif
                    @if(session('error')) <div class="text-red-600">{{ session('error') }}</div> @endif

                    <h3>Добавить пользователя</h3>
                    <form method="POST" action="{{ route('admin.users.store') }}" class="mb-6">
                        @csrf
                        <div class="grid grid-cols-4 gap-2">
                            <input type="text" name="name" placeholder="Логин" required class="border">
                            <input type="email" name="email" placeholder="Email" required class="border">
                            <input type="password" name="password" placeholder="Пароль" required class="border">
                            <select name="role">
                                <option value="user">Пользователь</option>
                                <option value="admin">Админ</option>
                            </select>
                            <button type="submit" style="background:black;" class="bg-blue-500 text-white px-2">Добавить</button>
                        </div>
                    </form>

                    <h3>Список пользователей</h3>
                    <table class="w-full">
                        <thead>
                            <tr><th>ID</th><th>Логин</th><th>Email</th><th>Роль</th><th>Статус</th><th>Действия</th></tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role }}</td>
                                <td>@if($user->isLocked()) Заблокирован @else Активен @endif</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $user->name }}" class="w-20">
                                        <input type="email" name="email" value="{{ $user->email }}" class="w-24">
                                        <input type="password" name="password" placeholder="новый" class="w-20">
                                        <select name="role">
                                            <option value="user" @if($user->role=='user') selected @endif>user</option>
                                            <option value="admin" @if($user->role=='admin') selected @endif>admin</option>
                                        </select>
                                        <button type="submit">Сохранить</button>
                                    </form>
                                    @if($user->isLocked())
                                        <form method="POST" action="{{ route('admin.users.unlock', $user) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit">Разблокировать</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.lock', $user) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit">Заблокировать</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Удалить?')">
                                        @csrf @method('DELETE')
                                        <button type="submit">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="text-blue-600">← Назад в кабинет</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>