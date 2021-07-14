<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EmailNotificacao extends Controller
{
    public function envioSenha ($nomePessoa, $emailUsuario, $senhaBase) {
        $anoAtual = Carbon::now()->format('Y');

        $dadosPreenchimento = [
            'nomePessoa' => $nomePessoa,
            'emailUsuario' => $emailUsuario,
            'senhaBase' => $senhaBase,
            'anoAtual' => $anoAtual
        ];

        Mail::send('email.envioSenha', $dadosPreenchimento, function($mensagemEnvio) use ($emailUsuario, $nomePessoa)
        {
            $mensagemEnvio->from('suporte@mg.encontraja.com.br', 'Suporte EncontraJa')
            ->to($emailUsuario)
            ->subject('[EncontraJa] Bem - vindo ' . $nomePessoa . ' - Senha de acesso');
        });
    }

    public function avisoCadastroDeAnuncio($emailPessoa, $idAnuncio, $tituloAnuncio, $descricao) {
        $anoAtual = Carbon::now()->format('Y');

        if (is_null($descricao) == true) {
            $descricao = 'O anúncio não há descrição.';
        }

        $dadosPreenchimento = [
            'anoAtual' => $anoAtual,
            'urlAnuncio' => 'http://www.encontraja.com.br/anuncio/' . $idAnuncio
        ];

        Mail::send('email.avisoCriacaoAnuncio', $dadosPreenchimento, function($mensagemEnvio) use ($emailPessoa, $tituloAnuncio)
        {
            $mensagemEnvio->from('operacaoemconta@mg.encontraja.com.br', 'Operação Em Conta')
            ->to($emailPessoa)
            ->subject('[EncontraJa] Novo anúncio cadastrado - ' . $tituloAnuncio);
        });
    }
}

