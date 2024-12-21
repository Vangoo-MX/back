<?php

namespace App\Http\Controllers;

use App\Models\Apartments;
use App\Models\Developments;
use App\Models\Lots;
use App\Models\Properties;
use App\Models\Terrains;
use Illuminate\Http\Request;

class CommissionsController extends Controller
{
    public function getCommissionsEP($type)
    {
        switch ($type) {
            case 'dev':
                $model = Developments::class;
                break;
            case 'property':
                $model = Properties::class;
                break;
            case 'lot':
                $model = Lots::class;
                break;
            case 'apartment':
                $model = Apartments::class;
                break;
            case 'terrain':
                $model = Terrains::class;
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        $commissions = $model::whereNotNull('commission_percentage')
            ->distinct('commission_percentage')
            ->pluck('commission_percentage');

        return $commissions;
    }
}
