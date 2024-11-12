<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Roles;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsApartments;
use App\Models\Tracker;
use App\Models\Agenda;
use App\Models\Apartments;
use App\Models\ApartmentsHighlights;
use App\Models\ApartmentsQueue;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use App\Models\Lots;
use App\Models\LotsHighlights;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $activePropertiesCount = Properties::count();
        $pendingPropertiesCount = PropertiesQueue::where('status_aproved', '!=', 2)->count();
        $activeDevelopmentsCount = Developments::count();
        $properties = Properties::all();
        return view('admin.index', compact(
            'properties',
            'activePropertiesCount',
            'pendingPropertiesCount',
            'activeDevelopmentsCount'
        ));
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.create');
    }

    public function store(Request $request)
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $request->validate([
            'name' => 'required',
            'password' =>  ['required', 'min:8'],
            'tel' => 'numeric|required',
            'password_confirmation' => 'same:password',
            'email' => [
                'required',
                'email',
                Rule::unique('app_users')->ignore($request->user()),
            ]
        ], [
            'name.required' => 'El campo nombre es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password_confirmation.same' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'tel.numeric' => 'El campo teléfono debe ser numérico.',
            'tel.required' => 'El campo teléfono es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está en uso. Por favor, elige otro.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->rol = $request->rol;
        $user->tel = $request->tel;
        $user->save();

        return redirect()->route('admin.users');
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
        $app = DevelopmentsApartments::where('id_development', $id)->first();

        return response()->view('admin.editdev', compact('municipios', 'dev', 'app'))->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
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

    public function show($id = 0)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $user = User::find($id);
        $roles = Roles::all();

        return view('admin.user', compact('user'), compact('roles'));
    }

    public function allusers()
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $users = User::all();
        $roles = Roles::all();

        return view('admin.allusers', compact('users', 'roles'));
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $user = User::find($id);

        if ($user) {
            $extensions = ['jpg', 'jpeg', 'png'];

            foreach ($extensions as $extension) {
                $profileImagePath = storage_path("app/public/img/users/{$user->id}.{$extension}");
                if (file_exists($profileImagePath)) {
                    unlink($profileImagePath);
                    break;
                }
            }
            $user->delete();
            return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente');
        } else {
            return redirect()->route('admin.users')->with('error', 'No se pudo encontrar el usuario');
        }
    }

    public function contacts(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
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

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $request->validate([
            'name' => 'required',
            'tel' => [
                'numeric',
                'required',
                function ($attribute, $value, $fail) {
                    if (strlen($value) !== 10) {
                        $fail('El campo teléfono debe tener exactamente 10 dígitos.');
                    }
                }
            ],
            'biography' => 'max:250',
            'contact_schedule' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        if (!preg_match('/^\d{1,2}:\d{2} (am|pm) - \d{1,2}:\d{2} (am|pm)$/', $value)) {
                            $fail('El formato de la franja horaria debe ser como "8:00 am - 8:00 pm".');
                        }
                    }
                },
            ],
            'profile_image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'El campo nombre es obligatorio.',
            'tel.numeric' => 'El campo teléfono debe ser numérico.',
            'tel.required' => 'El campo teléfono es obligatorio.',
            'biography.max' => 'Su biografia no debe de exceder los 250 caracteres',
            'contact_schedule.required' => 'El campo horario de contacto es obligatorio.',
            'profile_image.image' => 'El archivo debe ser una imagen.',
            'profile_image.mimes' => 'El archivo debe ser una imagen jpeg, png o jpg.',
            'profile_image.max' => 'El archivo no debe pesar más de 2MB.',
        ]);
        if ($request->hasFile('profile_image')) {
            $userId = $user->id;
            $filename = $userId . "." . $request->profile_image->extension();
            $request->profile_image->storeAs('public/img/users', $filename);
            $updateData = ['profile_image' => $filename];
            if ($request->filled(['name', 'tel', 'biography', 'email', 'rol', 'contact_preference', 'contact_schedule'])) {
                $updateData = array_merge($updateData, [
                    'name' => $request->name,
                    'tel' => $request->tel,
                    'biography' => $request->biography,
                    'email' => $request->email,
                    'rol' => $request->rol,
                    'contact_preference' => $request->contact_preference,
                    'contact_schedule' => $request->contact_schedule,
                ]);
            }

            $user->update($updateData);
            return redirect()->route('admin.user', $user)->with('success', 'Perfil de usuario actualizado correctamente');
        } else {
            $user->update([
                'name' => $request->name,
                'tel' => $request->tel,
                'biography' => $request->biography,
                'email' => $request->email,
                'rol' => $request->rol,
                'contact_preference' => $request->contact_preference,
                'contact_schedule' => $request->contact_schedule,
            ]);
            return redirect()->route('admin.user', $user)->with('success', 'Usuario actualizado correctamente');
        }
    }
    public function password(User $user)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.changepassword', compact('user'));
    }
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' =>  ['required', 'min:8'],
            'password_confirmation' => 'same:password',
        ], [
            'password.required' => 'El campo contraseña es obligatorio.',
            'password_confirmation.same' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);
        $user->update(['password' => $request->password]);
        return redirect()->route('admin.user', $user)->with('success', 'Contraseña actualizada correctamente');
    }
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

    public function updateProperties(Request $request, Properties $propiedad)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $updates = [];
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($propiedad->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;
        if ($request->hasFile('images')) {
            $numImages = $propiedad->images + sizeof($request->file('images'));
        } else {
            $numImages = $propiedad->images;
        }
        $propiedad->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'type' => $request->type,
            'price' => $request->price,
            'price_maintenance' => $request->price_maintenance,
            'description' => $request->description,
            'rooms' => $request->rooms,
            'bathrooms' => $request->bathrooms,
            'parkings' => $request->parkings,
            'map' => $request->map,
            'area' => $request->area,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'num_int' => $request->num_int,
            'cp' => $request->cp,
            'amenities' => $request->amenities,
            'services' => $request->services,
            'sell_type' => $request->sell_type,
            'share_conditions' => $request->share_conditions,
            'antiquity' => $request->antiquity,
            'location' => $location,
            'images' => $numImages
        ]);

        if ($request->type == "departamento") {
            $updates['floor'] = $request->floor;
            $updates['dev_type'] = $request->dev_type;
        }

        if ($request->type == "terreno") {
            $updates['area_terrain'] = $request->area_terrain;
        }

        if ($request->price_m2) {
            $updates['price_m2'] = $request->price_m2;
        }

        if (!empty($updates)) {
            $propiedad->update($updates);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                $nameimg = Str::slug($propiedad->images + $index + 1) . "." . $image->getClientOriginalExtension();
                $image->storeAs('public/img/posts/properties/' . $propiedad->id . '/', $nameimg);
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/properties/' . $propiedad->id);
                $key = $index;
                if (file_exists($path . "/{$order}.jpg")) {
                    rename($path . "/{$order}.jpg", $path . "/{$order}temp.jpg");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/properties/' . $propiedad->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.jpg")) {
                    rename($path . "/{$key}temp.jpg", $path . "/{$order}.jpg");
                }
            }
        }

        return redirect()->route('admin.details', $propiedad)->with('success', 'Propiedad actualizada correctamente');
    }

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

    public function editApartmentPage($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $apartment = Apartments::find($id);
        $municipio_propiedad = Municipios::find($apartment->id_municipio);
        $estado_propiedad = $municipio_propiedad->id_estado;
        $municipios = Municipios::where('id_estado', $estado_propiedad)->get();
        $colonias = Colonias::where('id_municipio', $apartment->id_municipio)->get();

        return view('admin.editApartmentPage', compact('municipios', 'colonias', 'apartment'));
    }

    public function editdevpage($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado', 19)->get();

        $dev = Developments::where('id', $id)->get();
        $app = DevelopmentsApartments::where('id_development', $id)->get();

        return response()->view('admin.editdev', compact('municipios', 'dev', 'app'))->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function developments()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $desarrollos = Developments::get();

        return view('admin.developments', compact('desarrollos'));
    }

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

    public function highlightsApartments()
    {

        if (!Auth::check()) {
            return redirect('/');
        }
        if (Auth::user()->rol != 1) {
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $apartmentshl = ApartmentsHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsApartments', compact('apartmentshl', 'estados', 'municipios', 'municipiosh'));
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

    public function email_confirm()
    {
        return view('emails.confirm');
    }

    public function email_template()
    {
        return view('emails.template');
    }
}
