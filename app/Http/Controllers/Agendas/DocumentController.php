<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Collection;

class DocumentController extends Controller
{
    public function getDocuments(Agenda $agenda): Collection
    {
        return $agenda->agendaDocs;
    }
}
