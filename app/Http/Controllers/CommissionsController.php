<?php

namespace App\Http\Controllers;

use App\Models\Apartments;
use App\Models\Developments;
use App\Models\DevelopmentsHorizontals;
use App\Models\Lots;
use App\Models\Properties;
use App\Models\Terrains;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class CommissionsController extends Controller
{
    public function getCommissions(string $type): JsonResponse|Collection
    {
        $modelClass = match ($type) {
            'dev'           => Developments::class,
            'devHorizontal' => DevelopmentsHorizontals::class,
            'property'      => Properties::class,
            'lot'           => Lots::class,
            'apartment'     => Apartments::class,
            'terrain'       => Terrains::class,
            default        => null,
        };

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        return $modelClass::whereNotNull('commission_percentage')
            ->distinct()
            ->pluck('commission_percentage');
    }
}
