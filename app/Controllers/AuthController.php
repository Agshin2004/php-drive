<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function register(Request $request, Response $response)
    {
        try {
            $data = $request->getParsedBody();

            $email = $data['email'];
            $password = $data['password'];

            if (!$email || !$password) {
                return ResponseFactory::json(['error' => 'email and password are required'], 400);
            }

            // check if email is valid
            if (!str_contains($email, '@') || !str_contains($email, '.')) {
                return ResponseFactory::json(['error' => 'invalid email'], 400);
            }

            // add user to db
            $user = User::create([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            // create folder for user in user_space
            $username = explode('@', $email)[0];
            dd($username);
            
            // create jwt for user
            $jwt = generateJwt($user->id);

            return ResponseFactory::json([
                'user' => $user,
                'jwt' => $jwt
            ]);
        } catch (\Exception $e) {
            return ResponseFactory::json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request, Response $response)
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'];
            $password = $data['password'];

            if (!$email || !$password) {
                return ResponseFactory::json(['error' => 'email and password are required'], 400);
            }

            $user = User::where('email', $email)->first();
            if ($user && password_verify($password, $user->password)) {
                $jwt = generateJwt($user->id);

                return ResponseFactory::json([
                    'jwt' => $jwt
                ]);
            }

            return ResponseFactory::json([
                'error' => 'invalid email or password'
            ], 400);
        } catch (\Exception $e) {
            return ResponseFactory::json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function refresh()
    {
        throw new \Exception('NOT IMPLEMENTED', 500);
    }

    public function logout()
    {
        throw new \Exception('NOT IMPLEMENTED', 500);
    }
}
