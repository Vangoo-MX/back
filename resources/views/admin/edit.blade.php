@extends('layouts.adminLayout')

@section('breadcrumb','Editar usuario')

@section('title','Editar usuario')

@section('titleContent','Editar usuario')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center">
    <div class="col-12 col-lg-8 px-5 d-flex flex-column align-items-center justify-content-center">

        <h3>Editar usuario</h3>

        <div class="w-100">
            <form method="post" action="{{route('admin.update', $user->id)}}" class="w-100">

                @csrf

                <div class="row">
                    <div class="col-12 col-lg-6 mb-3 mt-3">
                        <div>
                            <label for="name" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" value="{{old('name', $user->name)}}" id="name" placeholder="Ingresa el nombre" name="name">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3 mt-3">
                        <div>
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" value="{{old('email', $user->email)}}" id="email" placeholder="Ingresa el correo electronico" name="email">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="biography" class="form-label">Biografia:</label>
                            <input type="text" class="form-control" value="{{old('biography', $user->biography)}}" id="biography" placeholder="Acerca de ti..." name="biography">
                            @error('biography')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="tel" class="form-label">Numero de telefono celular:</label>
                            <input type="tel" class="form-control" value="{{old('tel', $user->tel)}}" id="tel" placeholder="Ingresa el teléfono" name="tel">
                            @error('tel')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="preference" class="form-label">Preferencias de contacto:</label>
                            <select class="form-select" name="contact_preference" value="{{old('contact_preference', $user->contact_preference)}}" id="contact_preference">
                                <option>Correo Electronico</option>
                                <option>WhatsApp</option>
                                <option>Llamada Telefonica</option>
                            </select>
                            @error('contact_preference')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="schedule" class="form-label">Horario para contactar:</label>
                            <input type="text" class="form-control" name="contact_schedule" value="{{ old('contact_schedule', $user->contact_schedule) }}" id="contact_schedule" placeholder="Ejemplo: 8:00 am - 8:00 pm">
                            @error('contact_schedule')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="roluser" class="form-label">Rol de usuario:</label>
                            <select class="form-select" name="rol" id="rol">
                                <option value="1">Administrador</option>
                                <option value="2">Moderador</option>
                                <option value="3">Asesor</option>
                                <option value="4">Vendedor</option>
                                <option value="5">Propietario</option>
                                <option value="6">Usuario</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn1">Actualizar usuario</button>
                </div>

            </form>
        </div>

    </div>
</div>
<style>
    button.bg-gradient-info {
        background-color: var(--info);
        background-size: cover;
        color: white;
        border-radius: 25px;
    }

    button.bg-gradient-info:hover {
        background-color: var(--info);
        background-size: cover;
        opacity: 0.7;
        color: white;
    }
</style>

@endsection()
