<?php

namespace EncontraJa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnuncioServicoRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
           'tituloAnuncio' => 'required|string',
           'exibirData' => 'nullable|date_format:"d/m/Y"',
           'descricao' => 'string',
           'telefone' => 'string',
           'emailContato' => 'nullable|email:rfc,dns,filter',
           'cidadesAnuncio' => 'required'        
        ];
    }
}