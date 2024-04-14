<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
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

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;

use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.index');
    }

    public function create(){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.create');
    }

    public function store(Request $request){

        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('app_users')->ignore($request->user()),
            ]
        ], [
            'email.unique' => 'El correo electrónico ya está en uso. Por favor, elige otro.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        if($request->rol){
            $user->rol = $request->rol;
        }
        if($request->tel){
            $user->tel = $request->tel;
        }
        $user->save();

        return redirect()->route('admin.user', $user);
        
    }
    
    public function createdev(){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado',19)->get();

        return view('admin.createdev', compact('municipios'));
    }
    
    public function editdev($id){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado',19)->get();
        
        $dev = Developments::where('id',$id)->get();
        $app = DevelopmentsApartments::where('id_development',$id)->get();

        return response()->view('admin.editdev', compact('municipios','dev','app'))->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function show($id = 0){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $user = User::find($id);
        $roles = Roles::all();

        return view('admin.user', compact('user'),compact('roles'));
    }

    public function allusers(){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $users = User::all();
        $roles = Roles::all();

        return view('admin.allusers', compact('users'),compact('roles'));
    }

    public function contacts(){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $agenda = Agenda::join('app_users', 'list_agenda.id_user', '=', 'app_users.id')
        ->select('list_agenda.*', 'app_users.name as user_name')
        ->get();
        return view('admin.contacts', compact('agenda'));
    }

    public function edit(User $user){

        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.edit',compact('user'));

    }

    public function properties(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        
        $propiedades = Properties::get();

        return view('admin.properties',compact('propiedades'));
    }

    public function editdevpage($id){
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        $municipios = Municipios::where('id_estado',19)->get();
        
        $dev = Developments::where('id',$id)->get();
        $app = DevelopmentsApartments::where('id_development',$id)->get();

        return response()->view('admin.editdev', compact('municipios','dev','app'))->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function details($id){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        $municipios = Municipios::where('id_estado',19)->get();
        $propiedad = Properties::find($id);

        return view('admin.details',compact('propiedad'),compact('municipios'));
    }
    
    public function developments(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        
        $desarrollos = Developments::get();

        return view('admin.developments',compact('desarrollos'));
    }

    public function queue(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        
        $propiedadesqueue = PropertiesQueue::where('status_aproved',0)->get();

        $propiedadesrejected = PropertiesQueue::where('status_aproved',2)->get();

        $propiedadesrevision = PropertiesQueue::where('status_aproved',3)->get();

        return view('admin.queue',compact('propiedadesqueue','propiedadesrejected','propiedadesrevision'));
    }

    public function files(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.files');
    }

    public function statistics(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.statistics');
    }

    public function settingsinfo(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }

        return view('admin.settingsinfo');
    }

    public function highlights(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        
        $propertieshl = PropertiesHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight',1)->get();

        return view('admin.highlights',compact('propertieshl', 'estados', 'municipios', 'municipiosh'));
    }
    
    public function highlightsdev(){
       
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->rol != 1){
            return "Lo siento. No puedes ver esta página porque no eres un usuario administrador";
        }
        
        $devshl = DevelopmentsHighlights::get();
        $estados = Estados::get();
        $municipios = Municipios::get();
        $municipiosh = Municipios::where('highlight',1)->get();

        return view('admin.highlightsdev',compact('devshl', 'estados', 'municipios', 'municipiosh'));
    }

    public function email_confirm(){
        return view('emails.confirm');
    }

    public function email_template(){
        return view('emails.template');
    }


}
