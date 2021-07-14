<?php

namespace EncontraJa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstituicaoInformacaoRequest extends FormRequest
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
            'idLogin' => 'required|integer',
            'nomeInstituicao' => 'required|string',
            'dataAbertura' => 'required|date',
            'dataFechado' => 'date',
            'tipo' => 'required|string',
            'descricao' => 'required|string'
        ];
    }
}
