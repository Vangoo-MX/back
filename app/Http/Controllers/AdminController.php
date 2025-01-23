<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Roles;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsApartments;
use App\Models\Agenda;
use App\Models\Apartments;
use App\Models\ApartmentsHighlights;
use App\Models\ApartmentsQueue;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use App\Models\Lots;
use App\Models\LotsHighlights;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Models\TerrainsQueue;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    //index
    public function index()
    {

        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $data = [
            'activePropertiesCount' => Properties::count(),
            'pendingPropertiesCount' => PropertiesQueue::where('status_aproved', '!=', 2)->count(),
            'activeDevelopmentsCount' => Developments::count(),
            'properties' => Properties::all(),
        ];

        return view('admin.index', $data);
    }

    //users
    public function create()
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        return view('admin.create');
    }

    public function store(CreateUserRequest $request)
    {

        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'rol' => $request->rol,
            'tel' => $request->tel,
        ]);

        return redirect()->route('admin.users');
    }

    public function show($id = 0)
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $user = User::findOrFail($id);
        $roles = Roles::all();

        return view('admin.user', compact('user', 'roles'));
    }

    public function allusers()
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $users = User::all();
        $roles = Roles::all();

        return view('admin.allusers', compact('users', 'roles'));
    }

    public function destroy($id)
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'No se pudo encontrar el usuario.');
        }

        $extensions = ['jpg', 'jpeg', 'png'];
        foreach ($extensions as $extension) {
            $profileImagePath = storage_path("app/public/img/users/{$user->id}.{$extension}");
            if (file_exists($profileImagePath)) {
                unlink($profileImagePath);
                break;
            }
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente.');
    }

    public function contacts(Request $request)
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $selectedUserID = $request->input('user_id', Auth::id());

        $agenda = Agenda::join('app_users', 'list_agenda.id_user', '=', 'app_users.id')
            ->where('list_agenda.id_user', $selectedUserID)
            ->select('list_agenda.*', 'app_users.name as user_name')
            ->get();

        $users = User::whereIn('rol', [1, 2, 3, 4])->pluck('name', 'id');

        return view('admin.contacts', compact('agenda', 'users', 'selectedUserID'));
    }

    public function edit(User $user)
    {

        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        return view('admin.edit', compact('user'));
    }

    public function update(CreateUserRequest $request, User $user)
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        $updateData = $request->only([
            'name',
            'tel',
            'biography',
            'email',
            'rol',
            'contact_preference',
            'contact_schedule',
        ]);

        if ($request->hasFile('profile_image')) {
            $filename = $user->id . '.' . $request->profile_image->extension();
            $request->profile_image->storeAs('public/img/users', $filename);
            $updateData['profile_image'] = $filename;
        }

        $user->update($updateData);

        return redirect()->route('admin.user', $user)->with('success', 'Usuario actualizado correctamente.');
    }

    public function password(User $user)
    {
        if (!Auth::check() || Auth::user()->rol !== 1) {
            return redirect('/')->withErrors('No tienes permiso para acceder a esta página.');
        }

        return view('admin.changepassword', compact('user'));
    }

    public function updatePassword(CreateUserRequest $request, User $user)
    {
        $user->update(['password' => $request->password]);
        return redirect()->route('admin.user', $user)->with('success', 'Contraseña actualizada correctamente');
    }

    public function getColonias(Request $request)
    {
        try {
            $municipioId = $request->municipio_id;
            $colonias = Colonias::where('id_municipio', $municipioId)->get();
            return response()->json($colonias);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //properties
    public function properties()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $propiedades = Properties::get();

        return view('admin.properties', compact('propiedades'));
    }

    public function details($id)
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $municipios = Municipios::where('id_estado', 19)->get();
        $propiedad = Properties::find($id);

        return view('admin.details', compact('propiedad', 'municipios'));
    }

    public function showProperties($propiedad)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $propiedad = Properties::find($propiedad);
        $municipio_propiedad = Municipios::find($propiedad->id_municipio);
        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $propiedad->id_municipio)->get();

        return view('admin.showProperties', compact('municipios', 'colonias', 'propiedad'));
    }

    public function queue()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $propiedadesqueue = PropertiesQueue::where('status_aproved', 0)->get();

        $propiedadesrejected = PropertiesQueue::where('status_aproved', 2)->get();

        $propiedadesrevision = PropertiesQueue::where('status_aproved', 3)->get();

        return view('admin.queue', compact('propiedadesqueue', 'propiedadesrejected', 'propiedadesrevision'));
    }

    public function highlights()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $propertieshl = PropertiesHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlights', compact('propertieshl', 'estados', 'municipios', 'municipiosh'));
    }

    //apartments
    public function apartments()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $apartments = Apartments::get();

        return view('admin.apartments', compact('apartments'));
    }

    public function detailsApartments($id)
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $municipios = Municipios::where('id_estado', 19)->get();
        $apartment = Apartments::find($id);

        return view('admin.detailsApartments', compact('apartment', 'municipios'));
    }

    public function editApartmentPage($apartments)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $apartment = Apartments::find($apartments);
        $municipio_propiedad = Municipios::find($apartment->id_municipio);
        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $apartment->id_municipio)->get();

        return view('admin.editApartmentPage', compact('municipios', 'colonias', 'apartment'));
    }

    public function queueApartments()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $apartmentsQueue = ApartmentsQueue::where('status_aproved', 0)->get();

        $apartmentsRejected = ApartmentsQueue::where('status_aproved', 2)->get();

        $apartmentsRevision = ApartmentsQueue::where('status_aproved', 3)->get();

        return view('admin.queueApartments', compact('apartmentsQueue', 'apartmentsRejected', 'apartmentsRevision'));
    }

    public function highlightsApartments()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $apartmentshl = ApartmentsHighlights::with(['estado', 'municipio', 'apartment'])->get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsApartments', compact('apartmentshl', 'estados', 'municipios', 'municipiosh'));
    }

    //terrains
    public function terrains()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $terrains = Terrains::get();

        return view('admin.terrains', compact('terrains'));
    }

    public function detailsTerrains($id)
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $municipios = Municipios::where('id_estado', 19)->get();
        $terrain = Terrains::find($id);

        return view('admin.detailsTerrains', compact('terrain', 'municipios'));
    }

    public function editTerrainPage($terrains)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $terrain = Terrains::find($terrains);
        $municipio_propiedad = Municipios::find($terrain->id_municipio);
        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $terrain->id_municipio)->get();

        return view('admin.editTerrainPage', compact('municipios', 'colonias', 'terrain'));
    }

    public function queueTerrains()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $terrainsQueue = TerrainsQueue::where('status_aproved', 0)->get();

        $terrainsRejected = TerrainsQueue::where('status_aproved', 2)->get();

        $terrainsRevision = TerrainsQueue::where('status_aproved', 3)->get();

        return view('admin.queueTerrains', compact('terrainsQueue', 'terrainsRejected', 'terrainsRevision'));
    }

    public function highlightsTerrains()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $terrainshl = TerrainsHighlights::with(['estado', 'municipio', 'terrain'])->get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsTerrains', compact('terrainshl', 'estados', 'municipios', 'municipiosh'));
    }

    //developments
    public function developments(Request $request)
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $selectedMode = $request->input('mode', 'all');

        $desarrollos = Developments::when($selectedMode !== 'all', function ($query) use ($selectedMode) {
            return $query->where('mode', $selectedMode);
        })->get();

        return view('admin.developments', compact('desarrollos', 'selectedMode'));
    }

    public function createdev()
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado', 19)->get();

        return view('admin.createdev', compact('municipios'));
    }

    public function editdev($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado', 19)->get();

        $dev = Developments::where('id', $id)->first();
        $app = DevelopmentsApartments::where('id_development', $id)->get();

        return response()->view('admin.editdev', compact('municipios', 'dev', 'app'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function highlightsdev()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $devshl = DevelopmentsHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsdev', compact('devshl', 'estados', 'municipios', 'municipiosh'));
    }

    //lots
    public function lots()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $lots = Lots::get();

        return view('admin.lots', compact('lots'));
    }

    public function createLot()
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado', 19)->get();

        return view('admin.createlot', compact('municipios'));
    }

    public function editLotPage($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado', 19)->get();

        $lot = Lots::where('id', $id)->get();

        return response()
            ->view('admin.editlot', compact('municipios', 'lot'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function highlightsLot()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $lotshl = LotsHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightslots', compact('lotshl', 'estados', 'municipios', 'municipiosh'));
    }

    //various
    public function files()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.files');
    }

    public function statistics()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.statistics');
    }

    public function settingsinfo()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.settingsinfo');
    }

    public function email_confirm()
    {
        return view('emails.confirm');
    }

    public function email_template()
    {
        return view('emails.template');
    }
}
