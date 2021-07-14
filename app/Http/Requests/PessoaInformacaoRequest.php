<?php

namespace EncontraJa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PessoaInformacaoRequest extends FormRequest
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
            'nomePessoa' => 'required|string',
            'email' => 'required|email:rfc,dns,filter',
            'dataNascimento' => 'required|date',
            'sexo' => 'required|integer',
            'aceitatermos' => 'required'
        ];
    }
}
