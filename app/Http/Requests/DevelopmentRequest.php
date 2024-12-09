<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevelopmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|min:5|max:100',
            'price_min' => 'required|numeric|lte:price_max',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|min:10|max:500',
            'availability' => 'required|date',
            'street' => 'required|min:3|max:20',
            'num_ext' => 'required|numeric',
            'cp' => 'required|numeric',
            'amenities' => 'min:3',
            'area' => 'required|numeric',
            'commission_percentage' => 'required|numeric',
            'id_municipio' => 'required',
            'images' => 'nullable|array',
            'option' => 'nullable|array',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.min' => 'El título debe tener más de 10 caracteres',
            'title.max' => 'El título debe tener menos de 100 caracteres',
            'price_min.required' => 'El precio mínimo es obligatorio',
            'price_min.numeric' => 'El precio mínimo debe ser un número',
            'price_min.lte' => 'El precio mínimo debe ser menor o igual al precio máximo',
            'price_max.required' => 'El precio máximo es obligatorio',
            'price_max.numeric' => 'El precio máximo debe ser un número',
            'price_max.gte' => 'El precio máximo debe ser mayor o igual al precio mínimo',
            'description.required' => 'La descripción es obligatoria',
            'description.min' => 'La descripción debe tener más de 10 caracteres',
            'description.max' => 'La descripción debe tener menos de 500 caracteres',
            'availability.required' => 'La fecha de disponibilidad es obligatoria',
            'availability.date' => 'La fecha de disponibilidad debe ser una fecha válida',
            'street.required' => 'La calle es requerida',
            'street.min' => 'La calle debe tener más de 3 caracteres',
            'street.max' => 'La calle debe tener menos de 100 caracteres',
            'num_ext.required' => 'El número exterior es requerido',
            'num_ext.numeric' => 'El número exterior solo puede ser un número',
            'cp.required' => 'El código postal es requerido',
            'cp.numeric' => 'El código solo puede ser un número',
            'amenities.min' => 'Ingrese al menos una amenidad',
            'area.required' => 'La medida del área es requerida',
            'area.numeric' => 'La medida del área debe ser un número',
            'commission_percentage.required' => 'El porcentaje de comisión es requerido',
            'commission_percentage.numeric' => 'El porcentaje de comisión debe ser un número',
            'id_municipio.required' => 'El municipio es requerido',
            'images.array' => 'Las imágenes deben ser un arreglo',
            'option.array' => 'Las opciones deben ser un arreglo',
        ];
    }
}
