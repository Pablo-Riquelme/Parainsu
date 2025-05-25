<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // Asegúrate de importar el modelo Role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hashing de contraseñas

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todos los usuarios.
     */
    public function index(Request $request) // Inyectamos Request
    {
        // Obtener la lista de roles para el filtro
        $roles = Role::orderBy('name')->get();

        // Iniciar la consulta de usuarios
        $query = User::query();

        // Aplicar filtros basados en los parámetros de la solicitud

        // 1. Búsqueda general por texto (nombre, email)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // 2. Filtrar por rol
        if ($request->filled('role_filtro')) {
            $roleFiltro = $request->input('role_filtro');
            $query->where('role_id', $roleFiltro);
        }

        // Obtener los usuarios paginados con los filtros aplicados
        $users = $query->paginate(10)->withQueryString();

        // Pasar los usuarios y roles a la vista
        return view('users.index', compact('users', 'roles'));
    }

    // ... (Métodos create, store, show, edit, update, destroy permanecen igual) ...

    public function create()
    {
        $roles = Role::all(); // Obtener todos los roles
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $userData = $request->except('password', 'password_confirmation');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}