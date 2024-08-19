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

    public function agendaRelation()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id');
    }
}
