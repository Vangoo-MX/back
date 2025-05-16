<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaDocs extends Model
{
    protected $table = 'listac_agenda_docs';

    protected $fillable = [
        'id_agenda',
        'contacto_cliente',
        'papeleria',
        'visita_casas',
        'seleccion_casa',
        'seleccion_tipo_credito',
        'ingreso_papeleria',
        'respuesta_bancos',
        'seleccion_financiamiento',
        'firma_carta_promesa',
        'firma_carta_comision',
        'seleccion_notaria',
        'solicitud_avaluo',
        'revision_papeleria',
        'ingreso_pre_preventivo',
        'recepcion_avaluo',
        'recepcion_pre_preventivo',
        'ingreso_infonavit',
        'recepcion_infonavit',
        'cierre_numeros',
        'realizacion_contratos',
        'autorizacion_contratos',
        'firma_contrato',
        'pago_comision',
        'nota_admin',
        'nota_vendedor',
    ];

    const CREATED_AT = 'timestamp_create';
    const UPDATED_AT = 'timestamp_update';

    public function agendaRelation(): BelongsTo
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id');
    }
}
