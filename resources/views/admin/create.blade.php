@extends('layouts.adminLayout')

@section('breadcrumb','Crear usuario')

@section('title','Crear usuario')

@section('titleContent','Crear usuario')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center">
        <div class="col-12 col-lg-4 px-5 d-flex flex-column align-items-center justify-content-center">

            <h3>Crear nuevo usuario</h3>

            <div class="w-100">
                <form method="post" action="{{route('admin.storeuser')}}" class="w-100">

                    @csrf

                    <div class="mb-3 mt-3">
                        <label for="name" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter name" name="name">
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="pwd" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña:</label>
                        <input type="password" class="form-control" id="password_confirmation" placeholder="Enter password again" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label for="tel" class="form-label">Teléfono:</label>
                        <input type="tel" class="form-control" id="tel" placeholder="Ingresa el teléfono" name="tel">
                    </div>
                    <div class="mb-3">
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
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn1">Crear</button>    
                    </div>
                    
                </form>
            </div>
            

        </div>
</div>

 <style>
    button.bg-gradient-info {
        background-color: var(--info);
        background-size: cover;
        color:white;
        border-radius:25px;
    }
    button.bg-gradient-info:hover {
        background-color: var(--info);
        background-size: cover;
        opacity:0.7;
        color:white;
    }
 </style>

@endsection()