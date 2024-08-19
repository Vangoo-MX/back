@component('mail::message')
# Nota Vendedor Actualizada

La nota del vendedor ha sido actualizada.

**ID Agenda:** {{ $agenda->id_agenda }}
**Nota Nueva:** {{ $agenda->nota_vendedor }}

Gracias,
{{ config('app.name') }}
@endcomponent
