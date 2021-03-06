<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClienteExpressStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if ($errors->has('PersEmail.unique') || $errors->has('CliNit.unique') || $errors->has('PersDocNumber.unique')) {
            return true;
        // }else{
            // return false;
        // }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'CliNit' => ['required','min:13','max:15'],
            'CliName'       => 'required|max:255|min:1',

            'SedeAddress'   => 'required|max:255|min:1',
            'SedePhone1'    => 'max:11|min:11|nullable',
            'FK_SedeMun'    => 'required|numeric|min:1|max:1122',

            'PersFirstName' => 'required|max:64|min:1',
            'PersLastName'  => 'required|max:64|min:1',
            'PersEmail'     => 'required|email|max:255',
            'PersCellphone' => 'required|max:12|min:12',
            'SedeMapLocalidad' => 'sometimes|max:50',
            'SedeMapAddressSearch' => 'required',
            'SedeMapAddressResult' => 'required',
            'SedeMapLat' => 'required|numeric',
            'SedeMapLong' => 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'PersEmail.unique' => 'El campo "Correo Electrónico de la Persona de Contacto" ya esta en uso.',
            'CliShortname.required' => 'Error de validación (CliShortname)',
        ];
    }
}
