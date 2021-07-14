<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Controllers\HashBcrypt;
use EncontraJa\Http\Controllers\Termo;
use EncontraJa\Http\Requests\PessoaInformacaoRequest;
use Illuminate\Support\Carbon;

class PessoaInformacao extends Controller
{
    public function cadastraPessoa (PessoaInformacaoRequest $request) {
        $dadosPessoa = $request->all();

        $nomePessoa = $dadosPessoa['nomePessoa'];
        $email = $dadosPessoa['email'];
        $dataNascimento = $dadosPessoa['dataNascimento'];
        $sexo =  $dadosPessoa['sexo'];
        $classificacao = 0;
        $descricao = $dadosPessoa['descricao'];
        $aceitatermos = $dadosPessoa['aceitatermos'];

        $usuario = new User();

        $procuraEmailCadastrado = $this->verificaEmailCadastradoPessoa($email);

        if ($procuraEmailCadastrado == true) {
            $hash = new HashBcrypt();
            $termo = new Termo();
            $emailEnvio = new EmailNotificacao();

            $senhaUsuario = $hash->mostraHash(null, true);

            $usuario->email = $email;
            $usuario->password = $senhaUsuario['senhaMontada'];
            $usuario->save();

            // ID do termo aceito, referente ao cadastro do usuário e pessoa
            $tipoAceito = 1;

            $inserirPessoaInformacao = DB::table('pessoaInformacao')->insert([
                'idLogin' => $usuario->id,
                'nomePessoa' => $nomePessoa,
                'dataNascimento' => $dataNascimento,
                'sexo' => $sexo,
                'classificacao' => $classificacao,
                'descricao' => $descricao
            ]);

            $termo->salvarTermo($usuario->id, $tipoAceito, $aceitatermos);

            $emailEnvio->envioSenha($nomePessoa, $email,$senhaUsuario['senhaBase']);

            return response()->json([
                'status' => 200,
                'transacao' => 'OK'
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'erro' => 'E-mail já cadastrado. Tente outro!'
            ]);
        }
    }

    public function solicitaPessoa($id, $restrito) {
        $auxiliares = new Auxiliares();

        $pesquisaPessoa = DB::table('pessoaInformacao')->where('id', '=', $id)->get([
            'nomePessoa',
            'dataNascimento',
            'sexo',
            'classificacao',
            'descricao',
            'criadoEm'
            ])->first();

        $auxSexo = null;
        $auxDataNascimento = Carbon::createFromFormat('Y-m-d H:s:i', $pesquisaPessoa->dataNascimento)->format('d/m/Y');
        $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($pesquisaPessoa->criadoEm);

        switch ($pesquisaPessoa->sexo) {
            case 0:
                $auxSexo = 'Masculino';
                break;
            case 1:
                $auxSexo = 'Feminino';
                break;
            default:
                $auxSexo = 'N/A';
                break;
        }

        if ($restrito == true) {
            return [
                'nomePessoa' => $pesquisaPessoa->nomePessoa,
                'dataNascimento' => $auxDataNascimento,
                'sexo' => $auxSexo,
                'classificacao' => $pesquisaPessoa->classificacao,
                'descricao' => $pesquisaPessoa->descricao,
                'criadoEm' => $auxCriadoEm
            ];
        } else {
            return [
                'nomePessoa' => $pesquisaPessoa->nomePessoa,
                'classificacao' => $pesquisaPessoa->classificacao,
                'descricao' => $pesquisaPessoa->descricao
            ];
        }
    }

    public function solicitaIdPessoaDependeIdUsuario ($idUsuario) {
        $pessoaInformacao = DB::table('pessoaInformacao')->where('idLogin', '=', $idUsuario)->get('id')->first();
        $idPessoa = $pessoaInformacao->id;

        return $idPessoa;
    }

    public function solicitaIdUsuarioDependeIdPessoa ($idPessoa) {
        $pessoaInformacao = DB::table('pessoaInformacao')->where('id', '=', $idPessoa)->get('idLogin')->first();
        $idUsuario = $pessoaInformacao->idLogin;

        return $idUsuario;
    }

    public function perfilIdUsuario($tokenCarregado = null, $idPessoaCarregado) {
        $idPessoa = null;
        $restrito = false;

        if ($tokenCarregado == 'null' && !empty($idPessoaCarregado)) {
            $idPessoa = $idPessoaCarregado;
        } else {
            $token = $tokenCarregado;
            $procuraToken = new TokenUso();
            $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

            if (empty($idUsuario)) {
                return response()->json([
                    'erro' => 'Usuário não encontrado'
                ]);
            } else {
                $restrito = true;
                $idPessoa = $this->solicitaIdPessoaDependeIdUsuario($idUsuario);
            }
        }

        $pesquisaPessoa = $this->solicitaPessoa($idPessoa, $restrito);

        return response()->json($pesquisaPessoa);
    }

    public function recuperaEmailPessoa ($idPessoa) {
        $procuraEmail = DB::table('pessoaInformacao')
        ->join('users', 'pessoaInformacao.idLogin', '=', 'users.id')
        ->where('pessoaInformacao.id', '=', $idPessoa)
        ->get(['users.email'])->first();

        return $procuraEmail->email;
    }

    public function verificaEmailCadastradoPessoa ($email) {
        $procuraEmail = DB::table('pessoaInformacao')
            ->join('users', 'pessoaInformacao.idLogin', '=', 'users.id')
            ->where('users.email', '=', $email)
            ->get(['users.email'])->first();

        if (empty($procuraEmail)) {
            return true;
        } else {
            return false;
        }
    }
}
