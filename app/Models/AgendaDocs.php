<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaDocs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
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

    public function agendaRelation()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id');
    }
}
