@extends('layouts.plantilla')

@section('title','Inicia Sesion')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-image">
    <div class="w-full max-w-md space-y-6 rounded-lg bg-white p-8 shadow-lg dark:bg-gray-900">
        <div class="space-y-2 text-center">
            <img src="/img/logo.png" alt="Logo" class="logo">
            <p class="text-gray-500 dark:text-gray-400">Ingresa tu correo electronico y tu contraseña para iniciar sesion</p>
        </div>
        @include('layouts.messages')
        @if (session('status'))
        <div class="bg-green-500 text-white p-4 rounded-md">
            {{ session('status') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-md">
            @foreach ($errors->all() as $error)
            {{ $error }}
            @endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('user.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-black focus:outline-none focus:ring-[#FF6492] focus:border-[#FF6492] sm:text-sm" placeholder="m@example.com" />
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <a href="{{route('password.request')}}" class="text-sm text-color hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="relative mt-1">
                    <input id="password" type="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-black focus:outline-none focus:ring-[#FF6492] focus:border-[#FF6492] sm:text-sm" placeholder="Ingresa tu contraseña" />
                </div>
            </div>
            <button type="submit" class="w-full btn-pink px-4 py-2 rounded-md">
                Inicia Sesion
            </button>
        </form>
        <div class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
            ¿No tienes una cuenta? <a href="https://www.vangoo.mx/bepartnercontact" class="font-medium text-color hover:underline">Registrate</a>
        </div>
    </div>
</div>
<style>
    .min-h-screen {
        min-height: 100vh;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-center {
        justify-content: center;
    }

    .bg-gray-100 {
        background-color: #f7fafc;
    }

    .dark .bg-gray-950 {
        background-color: #1a202c;
    }

    .w-full {
        width: 100%;
    }

    .max-w-md {
        max-width: 28rem;
    }

    .space-y-6>*+* {
        margin-top: 1.5rem;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .bg-white {
        background-color: #ffffff;
    }

    .p-8 {
        padding: 2rem;
    }

    .shadow-lg {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .dark .bg-gray-900 {
        background-color: #2d3748;
    }

    .text-center {
        text-align: center;
    }

    .text-3xl {
        font-size: 1.875rem;
    }

    .font-bold {
        font-weight: 700;
    }

    .text-gray-500 {
        color: #a0aec0;
    }

    .dark .text-gray-400 {
        color: #cbd5e0;
    }

    .space-y-4>*+* {
        margin-top: 1rem;
    }

    .block {
        display: block;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .border {
        border-width: 1px;
    }

    .border-gray-300 {
        border-color: #d2d6dc;
    }

    .rounded-md {
        border-radius: 0.5rem;
    }

    .shadow-sm {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .placeholder-gray-400 {
        color: #cbd5e0;
    }

    .focus\:outline-none:focus {
        outline: none;
    }

    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(255, 100, 146, 0.5);
    }

    .focus\:border-indigo-500:focus {
        border-color: #667eea;
    }

    .w-full {
        width: 100%;
    }

    .btn-pink {
        background-color: #FF6492;
        color: #ffffff;
    }

    .btn-pink:hover {
        background-color: #ff4a7f;
    }

    .btn-pink:focus {
        box-shadow: 0 0 0 2px rgba(255, 100, 146, 0.5);
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .hover\:underline:hover {
        text-decoration: underline;
    }

    .bg-image {
        width: 100%;
        height: 100vh;
        background-image: url('../img/bg.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top center;
        display: flex;
    }

    .logo {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 0 auto;
    }

    .justify-between {
        justify-content: space-between;
    }

    .text-color {
        color: #4FDC64;
    }

    .bg-green-500 {
        background-color: #48bb78;
    }

    .bg-red-500 {
        background-color: #f56565;
    }
</style>
@endsection()