@component('mail::message')
# Nota Administrador Actualizada

La nota del administrador {{$admin}} ha sido actualizada.

**ID Agenda:** {{$agenda->id_agenda}}


**Nota Nueva:** {{$agenda->nota_admin}}

Gracias,
{{ config('app.name') }}
@endcomponent
