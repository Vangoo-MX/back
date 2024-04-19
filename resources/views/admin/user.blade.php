@extends('layouts.adminLayout')

@section('breadcrumb','Usuario')

@section('title','Usuario')

@section('titleContent','Ver usuario')

@section('content')

<a href="{{ route('admin.edit', $user->id) }}">
    <button class="btn1">Editar usuario</button>
</a>
<div class="container py-5">

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center d-flex flex-column align-items-center" style="padding:54px 0 30px;">
                    <div class="img-fluid img-profile rounded-circle" style="width:150px;height:150px;">
                        <img src="{{asset('storage/img/users').'/'.$user->profile_image}}" alt="avatar" class="img-fluid rounded-circle" style="width:100%;height: 100%;object-fit:cover;border-radius:50%;" onerror="{this.src='{{url('./img/img404.jpg')}}'}">
                    </div>
                    <h5 class="my-3">{{$user->name}}</h5>
                    <p class="mb-1 {{ ($user->rol == 1) ? 'text-danger' : 'text-primary' }}">
                        @foreach($roles as $r)
                        {{($r->id == $user->rol) ? ucfirst($r->title) : ''}}
                        @endforeach
                    </p>
                    <p class="text-muted mb-4">Id de usuario: {{$user->id}}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Email</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{$user->email}}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Teléfono</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{$user->tel}}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Preferencia de contacto</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{$user->contact_preference}}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Horario de contacto</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{$user->contact_schedule}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.passChange', $user->id) }}">
                <button class="btn1">Cambiar contraseña</button>
            </a>
            <div class="card mb-4 mb-lg-0 d-none">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fas fa-globe fa-lg text-warning"></i>
                            <p class="mb-0">https://vangoo.mx</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                            <p class="mb-0">@user</p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection()