<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        Gate::authorize('manage-users');

        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('manage-users');

        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'warehouse'])],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('status', 'Usuario creado correctamente.');
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        try {
            DB::transaction(function () use ($user) {
                // If attempting to deactivate an admin, verify there is at least one other active admin
                if ($user->is_active && $user->isAdmin()) {
                    $activeAdminsCount = User::where('role', 'admin')
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->count();

                    if ($activeAdminsCount <= 1) {
                        throw new \RuntimeException('No se puede desactivar al único administrador activo del sistema.');
                    }
                }

                $user->is_active = ! $user->is_active;
                $user->save();
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        $mensaje = $user->is_active ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';

        return back()->with('status', $mensaje);
    }

    /**
     * Update the role of the specified user.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'No puedes cambiar tu propio rol.']);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'warehouse'])],
        ]);

        try {
            DB::transaction(function () use ($user, $validated) {
                // If demoting an admin to warehouse, verify there's another active admin
                if ($user->isAdmin() && $validated['role'] === 'warehouse' && $user->is_active) {
                    $activeAdminsCount = User::where('role', 'admin')
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->count();

                    if ($activeAdminsCount <= 1) {
                        throw new \RuntimeException('No se puede quitar el rol de administrador al único administrador activo.');
                    }
                }

                $user->role = $validated['role'];
                $user->save();
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('status', 'Rol de usuario actualizado exitosamente.');
    }
}
