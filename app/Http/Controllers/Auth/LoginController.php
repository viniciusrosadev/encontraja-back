<?php

namespace EncontraJa\Http\Controllers\Auth;

use EncontraJa\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use EncontraJa\Http\Controllers\HashBcrypt;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function validarLoginApi($email, $senha)
    {
        $usuarioLogin = DB::table('users')->where('email', '=', $email)->get()->first();

        if (empty($usuarioLogin)) {
            return response()->json(['status' => 'Usuário não encontrado']);
        } else {
            $hashCheca = new HashBcrypt();
            $retorno = $hashCheca->checaHash($senha, $usuarioLogin->password);

            switch ($retorno) {
                case true:

                    $tokenAcesso = md5($email . $senha . str_shuffle('encontraja'));

                    $verificaToken = DB::table('tokenUsuario')->where('idUsuario', '=', $usuarioLogin->id)->get()->first();

                    if (!empty($verificaToken->id)) {
                        $excluiToken = DB::table('tokenUsuario')->where('id', '=', $verificaToken->id)->delete();
                    }

                    $guardaToken = DB::table('tokenUsuario')->insert([
                        'idUsuario' => $usuarioLogin->id,
                        'token' => $tokenAcesso
                    ]);

                    if ($guardaToken) {
                        return response()->json([
                            'login' => 'confirmado',
                            'token' => $tokenAcesso
                        ]);
                    } else {
                        return response()->json([
                            'login' => 'erro durante a geração do acesso'
                        ]);
                    }
                    break;
                case false:
                    return response()->json(['login' => 'não autorizado']);
                    break;
                default:
                    return response()->json(['login' => $retorno]);
                    break;
            }
        }
    }
}
