<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Controllers\TokenUso;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Requests\ConfiguracoesPainelRequestAlterarEmail;
use EncontraJa\Http\Requests\ConfiguracoesPainelRequestSenha;
use EncontraJa\Http\Controllers\HashBcrypt;

class ConfiguracoesPainel extends Controller
{
    public function alterarEmail(ConfiguracoesPainelRequestAlterarEmail $request)
    {
        $operacaoRequest = $request->all();
        $emailNovo = $operacaoRequest['emailNovo'];
        $token = $operacaoRequest['token'];

        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $checaEmail = DB::table('users')->where('email', '=', $emailNovo)->get();

            if ($checaEmail->isEmpty()) {
                $atualizaEmail = DB::table('users')->where('id', '=', $idUsuario)
                ->update([
                    'email' => $emailNovo
                ]);

                $invalidarAcessoToken = $procuraToken->invalidarToken($idUsuario);
                
                if ($atualizaEmail == true && $invalidarAcessoToken == true) {
                    return response()->json([
                        'Transacao' => 'OK',
                        'Status' => 200
                    ]);
                } else {
                    return response()->json([
                        'erro' => 'Erro na operação de atualização'
                    ]);
                }
            } else {
                return response()->json([
                    'erro' => 'Erro na operação de verificação'
                ]);
            }
        }
    }

    public function alterarSenha(ConfiguracoesPainelRequestSenha $request) {
        $operacaoRequest = $request->all();

        $token = $operacaoRequest['token'];
        $senhaAntiga = $operacaoRequest['senhaAntiga'];
        $senhaNova = $operacaoRequest['senhaNova'];

        $hash = new HashBcrypt();
        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $senhaProcurada = DB::table('users')->where('id', '=', $idUsuario)->get(['password'])->first();

            $verificaSenha = $hash->checaHash($senhaAntiga, $senhaProcurada->password);

            if ($verificaSenha == true) {
                $novoHash = $hash->mostraHash($senhaNova);

                $atualizaSenha = DB::table('users')->where('id', '=', $idUsuario)->update([
                    'password' => $novoHash
                ]);

                $invalidarAcessoToken = $procuraToken->invalidarToken($idUsuario);

                if ($atualizaSenha == true && $invalidarAcessoToken == true) {
                    return response()->json([
                        'Transacao' => 'OK',
                        'Status' => 200
                    ]);
                } else {
                    return response()->json([
                        'erro' => 'Erro na operação de atualização'
                    ]);
                }
            } else {
                return response()->json([
                    'erro' => 'Erro na operação de verificação do hash'
                ]);
            }
        }
    }
}
