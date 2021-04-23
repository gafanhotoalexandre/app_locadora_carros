<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciais = $request->all(['email', 'password']);

        // autenticação (email e senha)
        $token = auth('api')->attempt($credenciais);

        if ($token) {// Usuário autenticado com sucesso

        // retornar um JWT (Json Web Token)
            return response()->json([
                'token' => $token
            ], 200);            
        } else {// Erro de usuário ou senha
            return response()->json([
                'erro' => 'Usuário ou Senha inválido!'
            ], 403);
            
            // erro 401 = unauthorized -> não autorizado
            // erro 403 = forbidden -> proibido (login inválido)
        }

        return 'login';
    }

    public function logout()
    {
        return 'logout';
    }

    public function refresh()
    {
        return 'refresh';
    }

    public function me()
    {
        return 'me';
    }
}
