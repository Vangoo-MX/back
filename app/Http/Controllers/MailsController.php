<?php

namespace App\Http\Controllers;

use App\Mail\BePartnerContactMail;
use App\Mail\ContactAgentMail;
use App\Mail\SalesAdvisorMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailsController extends Controller
{
    public function bePartner(Request $request)
    {
        Mail::to('bepartner@vangoo.mx')->send(new BePartnerContactMail($request->all()));
        return response()->json(['message' => 'Correo enviado con éxito'], 200);
    }

    public function contactAgent(Request $request)
    {
        Mail::to('agente@vangoo.mx')->send(new ContactAgentMail($request->all()));
        return response()->json(['message' => 'Correo enviado con éxito'], 200);
    }

    public function salesAdvisor(Request $request)
    {
        Mail::to('AsesoresdeVentas@Vangoo.mx')->send(new SalesAdvisorMail($request->all()));
        return response()->json(['message' => 'Correo enviado con éxito'], 200);
    }
}
