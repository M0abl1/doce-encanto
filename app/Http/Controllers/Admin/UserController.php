<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-staff');

        return view('admin.users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->only(['name', 'email', 'password']));
        // O papel fica fora do $fillable: só é definido aqui, numa rota que exige administrador.
        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return redirect()->route('admin.users.index')->with('status', "Funcionário {$user->name} cadastrado.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-staff');

        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')->with('error', 'Você não pode excluir a sua própria conta.');
        }

        // Transação com bloqueio: duas exclusões simultâneas não deixam a loja sem administrador.
        $deleted = DB::transaction(function () use ($user) {
            $admins = User::where('is_admin', true)->lockForUpdate()->pluck('id');
            if ($user->is_admin && $admins->count() <= 1) {
                return false;
            }

            return (bool) $user->delete();
        });

        if (! $deleted) {
            return redirect()->route('admin.users.index')->with('error', 'É preciso manter pelo menos um administrador.');
        }

        return redirect()->route('admin.users.index')->with('status', "Funcionário {$user->name} removido.");
    }
}
