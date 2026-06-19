<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $users = User::all();
        return view('admin.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'role' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin')->with('success', 'Пользователь добавлен');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|unique:users,name,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:user,admin',
            'password' => 'nullable|min:4',
        ]);

        $data = $request->only('name', 'email', 'role');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin')->with('success', 'Обновлено');
    }

    public function lock(User $user)
    {
        $user->lock();
        return back()->with('success', 'Заблокирован');
    }

    public function unlock(User $user)
    {
        $user->unlock();
        return back()->with('success', 'Разблокирован');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Нельзя удалить себя');
        $user->delete();
        return back()->with('success', 'Удалён');
    }
}