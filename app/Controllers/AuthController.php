<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\DirService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function register(Request $request, Response $response)
    {
        $parsedBody = $request->getParsedBody();

        $email = $parsedBody['email'];
        $username = $parsedBody['username'];
        $password = $parsedBody['password'];

        if (!$email || !$password || !$username) {
            return ResponseFactory::json(['error' => 'email, username, password are required'], 400);
        }

        // check if email is valid
        if (!str_contains($email, '@') || !str_contains($email, '.')) {
            return ResponseFactory::json(['error' => 'invalid email'], 400);
        }

        if (strlen($password) < 6) {
            return ResponseFactory::json(['error' => 'password must be at least 6 characters'], 400);
        }

        if (User::where('email', $email)->first() || User::where('username', $username)->first()) {
            return ResponseFactory::json(['error' => 'user with this email or username already exists'], 400);
        }

        // create folder for user in user_space
        $userFolderPath = DirService::createUserFolder($username);

        if (!$userFolderPath) {
            throw new \Exception('Unexpected error occured when creating user folder.', 500);
        }

        // add user to db
        $user = User::create([
            'email' => $email,
            'username' => $username,
            'user_folder_name' => $username,
            'user_folder_path' => $userFolderPath,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // create jwt for user
        $jwt = generateJwt($user->id);

        return ResponseFactory::json([
            'user' => $user,
            'jwt' => $jwt
        ]);
    }

    public function login(Request $request, Response $response)
    {
        $parsedBody = $request->getParsedBody();
        $email = $parsedBody['email'];
        $password = $parsedBody['password'];

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
