<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Http\Requests\DevelopmentRequest;
use App\Models\Colonias;
use App\Models\DevelopmentsHorizontalApartments;
use App\Models\DevelopmentsHorizontals;
use App\Models\Estados;
use App\Models\Municipios;
use Illuminate\Support\Facades\Auth;

class DevelopmentsHorizontalController
{
    public function storeDevHorizontal(DevelopmentRequest $request)
    {
        $development = new DevelopmentsHorizontals();
        $development->title = $request->title;
        $development->status = $request->status;
        $development->price_min = $request->price_min;
        $development->price_max = $request->price_max;
        $development->description = $request->description;
        $development->availability = $request->availability;
        $development->financing = $request->financing;
        $development->mode = $request->mode;
        $development->id_estado = $request->id_estado;
        $development->id_municipio = $request->id_municipio;
        $development->id_colonia = $request->id_colonia;
        $development->street = $request->street;
        $development->num_ext = $request->num_ext;

        $estado = Estados::find($request->id_estado)->nombre;
        $municipio = Municipios::find($request->id_municipio)->nombre;
        $colonia = Colonias::find($request->id_colonia)->nombre;

        $development->location = $colonia . ', ' . $municipio . ', ' . $estado;

        $development->cp = $request->cp;
        $development->map = $request->map;
        $development->map_lat = $request->map_lat;
        $development->map_long = $request->map_long;
        $development->area = $request->area;
        $development->amenities = $request->amenities;
        $development->commission_percentage = $request->commission_percentage;
        $development->id_user = Auth::user()->id;

        if ($request->hasFile('images')) {
            $development->images = count($request->file('images'));
        }

        $development->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/developmentsHorizontal/' . $development->id . '/');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);

                $imageName = Str::slug($index + 1) . '.webp';
                if ($image->getClientOriginalExtension() === 'webp') {
                    $image->move($path, $imageName);
                } else {
                    $imageWebp = Image::make($image->getRealPath())
                        ->encode('webp', 90);
                    $imageWebp->save($path . $imageName);
                }
            }
        }

        $key = 1;
        foreach ($request->option as $option) {
            $apartment = new DevelopmentsHorizontalApartments;
            $apartment->id_development = $development->id;
            $apartment->title = $option['title'];
            $apartment->price = $option['price'];
            $apartment->rooms = $option['rooms'];
            $apartment->bathrooms = $option['bathrooms'];
            $apartment->parkings = $option['parkings'];
            $apartment->area = $option['area'];
            if ($request->file('imageoption.' . $key) && is_array($request->file('imageoption.' . $key))) {
                $apartment->image_plans = sizeof($request->file('imageoption.' . $key));
            } elseif ($request->file('imageoption.' . $key) && !is_array($request->file('imageoption.' . $key))) {
                $apartment->image_plans = 1;
            } else {
                $apartment->image_plans = 0;
            }

            if ($option['num_available']) {
                $apartment->num_available = $option['num_available'];
            } else {
                $apartment->num_available = 0;
            }

            if (isset($request->imageoption[$key]) && $request->hasFile('imageoption.' . $key)) {
                $directory = 'public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/';
                $nameimg = Str::slug($key) . ".webp";
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);

                $extension = $request->file('imageoption.' . $key)->getClientOriginalExtension();

                if (strtolower($extension) !== 'webp') {
                    $imageWebp = Image::make($request->file('imageoption.' . $key)->getRealPath())
                        ->encode('webp', 90);
                    $imageWebp->save($path . $nameimg);
                } else {
                    $request->file('imageoption.' . $key)->storeAs($directory, $nameimg);
                }
            }

            $apartment->save();
            $key++;
        }
        return redirect()->route('admin.developmentsHorizontal');
    }
}
