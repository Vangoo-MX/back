@extends('layouts.plantilla')

@section('title','home')

@section('content')

<div class="sidenav">
    <div class="login-main-text">
        <h2>VANGOO</h2>
        <p>Inicia sesión para entrar.</p>
    </div>
</div>
<div class="main d-flex justify-content-center">
    <div class="col-md-5 col-sm-12">
        <div class="login-form">
            <h3 class="text-center">Login</h3>
            <form class="d-flex flex-column gap-4" method="POST" action="{{route('user.login')}}">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{old('email')}}">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                </div>
                @include('layouts.messages')
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        width: 100%;
        min-height: 100%;
        font-family: "Lato", sans-serif;
        position: relative;
    }

    .main-head {
        height: 150px;
        background: #FFF;
    }

    .sidenav {
        height: 100%;
        background-color: #FC7B97;
        overflow-x: hidden;
        padding-top: 30px;
    }

    .main {
        padding: 0px 10px;
    }

    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }
    }

    @media screen and (max-width: 450px) {
        .login-form {
            margin-top: 10%;
        }

        .register-form {
            margin-top: 10%;
        }
    }

    @media screen and (max-width: 750px) {
        .main {
            padding-top: 50px;
        }
    }

    @media screen and (min-width: 768px) {
        .main {
            margin-left: 40%;
        }

        .sidenav {
            width: 40%;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
        }

        .login-form {
            margin-top: 80%;
        }

        .register-form {
            margin-top: 20%;
        }
    }

    .login-main-text {
        margin-top: 20%;
        padding: 60px;
        color: black;
    }

    .login-main-text h2 {
        font-weight: 300;
    }

    .btn-primary {
        background-color: #FC7B97;
        border: 0;
    }

    .btn-primary:hover {
        background-color: #FC7B97;
        border: 0;
        opacity: 0.8;
    }

    .btn-secondary {
        background-color: #2E93EF;
        border: 0;
    }

    .btn-secondary:hover {
        background-color: #2E93EF;
        border: 0;
        opacity: 0.8;
    }
</style>

@endsection()
