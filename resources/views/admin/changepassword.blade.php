@extends('layouts.adminLayout')

@section('breadcrumb','Seguridad')

@section('title','Seguridad')

@section('titleContent','Cambiar Contraseña de Usuario')

@section('content')

<!-- Content Row -->
<div class="row d-flex justify-content-center">
    <div class="col-12 col-lg-8 px-5 d-flex flex-column align-items-center justify-content-center">

        <h3>Cambiar Contraseña de Usuario</h3>
        <br><br>

        <div class="w-100">
            <form method="post" action="{{route('admin.passUpdate', $user->id)}}" class="w-100">

                @csrf

                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="pwd" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password" placeholder="Debe de ser minimo 8 caracteres" name="password">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <div>
                            <label for="password_confirmation" class="form-label">Confirmar contraseña:</label>
                            <input type="password" class="form-control" id="password_confirmation" placeholder="Vuelve a ingresar la contraseña" name="password_confirmation">
                            @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn1">Cambiar Contraseña</button>
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
