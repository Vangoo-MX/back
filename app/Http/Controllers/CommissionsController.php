<?php

namespace App\Http\Controllers;

use App\Models\Developments;
use App\Models\Lots;
use App\Models\Properties;
use Illuminate\Http\Request;

class CommissionsController extends Controller
{
    public function getCommissionsEP($type)
    {
        if ($type == "dev") {
            $commissions = Developments::whereNotNull('commission_percentage')
                ->distinct('commission_percentage')
                ->pluck('commission_percentage');

            return $commissions;
        } else if ($type == "property") {
            $commissions = Properties::whereNotNull('commission_percentage')
                ->distinct('commission_percentage')
                ->pluck('commission_percentage');

            return $commissions;
        } else if ($type == "lot") {
            $commissions = Lots::whereNotNull('commission_percentage')
                ->distinct('commission_percentage')
                ->pluck('commission_percentage');
            return $commissions;
        }
    }
}
