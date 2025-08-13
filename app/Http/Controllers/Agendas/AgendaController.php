<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $selectedUserID = $request->integer('user_id', Auth::id());

        $agenda = Agenda::with('user:id,name,uuid')
            ->where('id_user', $selectedUserID)
            ->get();

        $validRoles = [
            UserRole::ADMINISTRADOR,
            UserRole::MODERADOR,
            UserRole::DESARROLLADOR,
            UserRole::VENDEDOR_ASOCIADO,
        ];

        $users = User::whereIn('rol', $validRoles)
            ->pluck('name', 'id');

        return view('admin.agenda.index', compact('agenda', 'users', 'selectedUserID'));
    }

    public function marcarComoLeido(Agenda $agenda): RedirectResponse
    {
        $agenda->update(['mensaje_leido' => true]);

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Mensaje marcado como leído exitosamente.');
    }
}
