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

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin');
    }

    //index
    public function index()
    {
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
        return view('admin.create');
    }

    public function store(CreateUserRequest $request)
    {
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
        $user = User::findOrFail($id);
        $roles = Roles::all();

        return view('admin.user', compact('user', 'roles'));
    }

    public function allusers()
    {
        $users = User::all();
        $roles = Roles::all();

        return view('admin.allusers', compact('users', 'roles'));
    }

    public function destroy($id)
    {
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
        return view('admin.edit', compact('user'));
    }

    public function update(CreateUserRequest $request, User $user)
    {
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
        return view('admin.changepassword', compact('user'));
    }

    public function updatePassword(CreateUserRequest $request, User $user)
    {
        $user->update(['password' => $request->password]);
        return redirect()->route('admin.user', $user)->with('success', 'Contraseña actualizada correctamente');
    }

    public function getColonias(Request $request)
    {
        $municipioId = $request->input('municipio_id');

        if (!$municipioId) {
            return response()->json(['error' => 'El ID del municipio es requerido.'], 400);
        }

        try {
            $colonias = Colonias::where('id_municipio', $municipioId)->get();
            return response()->json($colonias, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener las colonias.'], 500);
        }
    }

    //properties
    public function properties()
    {
        $propiedades = Properties::get();

        return view('admin.properties', compact('propiedades'));
    }

    public function details($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $propiedad = Properties::find($id);

        return view('admin.details', compact('propiedad', 'municipios'));
    }

    public function showProperties($propiedad)
    {
        $propiedad = Properties::find($propiedad);

        if (!$propiedad) {
            return redirect()->route('admin.properties')->withErrors('La propiedad no existe.');
        }

        $municipio_propiedad = Municipios::find($propiedad->id_municipio);

        if (!$municipio_propiedad) {
            return redirect()->route('admin.properties')->withErrors('El municipio de la propiedad no existe.');
        }

        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $propiedad->id_municipio)->get();

        return view('admin.showProperties', compact('municipios', 'colonias', 'propiedad'));
    }

    public function queue()
    {
        $propiedadesqueue = PropertiesQueue::where('status_aproved', 0)->get();
        $propiedadesrejected = PropertiesQueue::where('status_aproved', 2)->get();
        $propiedadesrevision = PropertiesQueue::where('status_aproved', 3)->get();

        return view('admin.queue', compact('propiedadesqueue', 'propiedadesrejected', 'propiedadesrevision'));
    }

    public function highlights()
    {
        $propertieshl = PropertiesHighlights::all();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlights', compact('propertieshl', 'estados', 'municipios', 'municipiosh'));
    }

    //apartments
    public function apartments()
    {
        $apartments = Apartments::get();

        return view('admin.apartments', compact('apartments'));
    }

    public function detailsApartments($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $apartment = Apartments::find($id);

        return view('admin.detailsApartments', compact('apartment', 'municipios'));
    }

    public function editApartmentPage($apartments)
    {
        $apartment = Apartments::find($apartments);

        if (!$apartment) {
            return redirect()->route('admin.apartments')->withErrors('El apartamento no existe.');
        }

        $municipio_propiedad = Municipios::find($apartment->id_municipio);

        if (!$municipio_propiedad) {
            return redirect()->route('admin.apartments')->withErrors('El municipio del apartamento no existe.');
        }

        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $apartment->id_municipio)->get();

        return view('admin.editApartmentPage', compact('municipios', 'colonias', 'apartment'));
    }

    public function queueApartments()
    {
        $apartmentsQueue = ApartmentsQueue::where('status_aproved', 0)->get();

        $apartmentsRejected = ApartmentsQueue::where('status_aproved', 2)->get();

        $apartmentsRevision = ApartmentsQueue::where('status_aproved', 3)->get();

        return view('admin.queueApartments', compact('apartmentsQueue', 'apartmentsRejected', 'apartmentsRevision'));
    }

    public function highlightsApartments()
    {
        $apartmentshl = ApartmentsHighlights::with(['estado', 'municipio', 'apartment'])->get();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsApartments', compact('apartmentshl', 'estados', 'municipios', 'municipiosh'));
    }

    //terrains
    public function terrains()
    {
        $terrains = Terrains::get();

        return view('admin.terrains', compact('terrains'));
    }

    public function detailsTerrains($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $terrain = Terrains::find($id);

        return view('admin.detailsTerrains', compact('terrain', 'municipios'));
    }

    public function editTerrainPage($terrains)
    {
        $terrain = Terrains::find($terrains);

        if (!$terrain) {
            return redirect()->route('admin.terrains')->withErrors('El terreno no existe.');
        }

        $municipio_propiedad = Municipios::find($terrain->id_municipio);

        if (!$municipio_propiedad) {
            return redirect()->route('admin.terrains')->withErrors('El municipio del terreno no existe.');
        }

        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $terrain->id_municipio)->get();

        return view('admin.editTerrainPage', compact('municipios', 'colonias', 'terrain'));
    }

    public function queueTerrains()
    {
        $terrainsQueue = TerrainsQueue::where('status_aproved', 0)->get();

        $terrainsRejected = TerrainsQueue::where('status_aproved', 2)->get();

        $terrainsRevision = TerrainsQueue::where('status_aproved', 3)->get();

        return view('admin.queueTerrains', compact('terrainsQueue', 'terrainsRejected', 'terrainsRevision'));
    }

    public function highlightsTerrains()
    {
        $terrainshl = TerrainsHighlights::with(['estado', 'municipio', 'terrain'])->get();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsTerrains', compact('terrainshl', 'estados', 'municipios', 'municipiosh'));
    }

    //developments
    public function developments(Request $request)
    {
        $selectedMode = $request->input('mode', 'all');

        $desarrollos = $selectedMode === 'all'
            ? Developments::all()
            : Developments::where('mode', $selectedMode)->get();

        return view('admin.developments', compact('desarrollos', 'selectedMode'));
    }

    public function createdev()
    {
        $municipios = Municipios::where('id_estado', 19)->get();

        return view('admin.createdev', compact('municipios'));
    }

    public function editdev($id)
    {
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
